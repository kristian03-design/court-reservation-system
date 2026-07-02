<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::where('published', true);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sport filter
        if ($request->filled('sport') && $request->sport !== 'all') {
            if ($request->sport === 'teams') {
                $query->whereIn('sport', ['Basketball', 'Futsal', 'Volleyball']);
            } elseif ($request->sport === 'players') {
                $query->whereIn('sport', ['Social Play', 'Tennis', 'Padel', 'Badminton']);
            } else {
                $query->where('sport', $request->sport);
            }
        }

        // Status filter
        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'active') {
                $query->where('status', 'open');
            } elseif ($filter === 'eliminated') {
                $query->where('status', 'closed');
            } elseif ($filter === 'seed') {
                $query->where('featured', true);
            }
        }

        $events = $query->orderBy('featured', 'desc')->orderBy('start_date', 'asc')->paginate(9);

        // My registered events (for logged-in users)
        $joinedEvents = collect();
        if (auth()->check()) {
            $joinedEvents = auth()->user()->eventRegistrations()
                ->with('event')
                ->get()
                ->pluck('event')
                ->filter();
        }

        return view('guest.guest-events', compact('events', 'joinedEvents'));
    }

    public function show(string $slug): View
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        // Fetch related events (matching sport or category, excluding self)
        $relatedEvents = Event::where('published', true)
            ->where('id', '!=', $event->id)
            ->where('sport', $event->sport)
            ->take(3)
            ->get();

        return view('guest.guest-event-details', compact('event', 'relatedEvents'));
    }

    public function registerForm(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Check if user is already registered
        $existing = $event->registrations()->where('user_id', auth()->id())->first();
        if ($existing) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'You are already registered for this event.');
        }

        // Check if event has open slots
        if ($event->registered >= $event->max_slots && !$event->allow_waitlist) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'This event is fully booked.');
        }

        return view('guest.guest-event-checkout', compact('event'));
    }

    public function submitRegistration(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Validation
        $rules = [
            'payment_method' => 'required|in:credit_card,gcash',
        ];

        if ($event->price > 0) {
            if ($request->payment_method === 'credit_card') {
                $rules += [
                    'card_number' => 'required|string|min:15|max:19',
                    'card_expiry' => 'required|string|regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/',
                    'card_cvc' => 'required|string|digits_between:3,4',
                ];
            } else {
                $rules += [
                    'proof_image' => 'required|image|max:4096',
                    'reference_number' => 'required|string|max:255',
                ];
            }
        }

        $request->validate($rules);

        // Check double booking again
        $existing = $event->registrations()->where('user_id', auth()->id())->first();
        if ($existing) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'You are already registered.');
        }

        // Determine statuses
        $isWaitlisted = $event->registered >= $event->max_slots;
        
        $paymentStatus = 'unpaid';
        $registrationStatus = 'pending';

        if ($event->price == 0) {
            $paymentStatus = 'paid';
            $registrationStatus = $isWaitlisted ? 'waitlisted' : 'confirmed';
        } else {
            if ($request->payment_method === 'credit_card') {
                $paymentStatus = 'paid';
                $registrationStatus = $isWaitlisted ? 'waitlisted' : 'confirmed';
            } else {
                // GCash
                $paymentStatus = 'pending_verification';
                $registrationStatus = 'pending';
            }
        }

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/payments'), $fileName);
            $proofImagePath = 'storage/payments/' . $fileName;
        }

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'payment_status' => $paymentStatus,
            'registration_status' => $registrationStatus,
            'payment_method' => $request->payment_method,
            'proof_image' => $proofImagePath,
            'reference_number' => $request->reference_number,
        ]);

        // Update registered count if confirmed
        if ($registrationStatus === 'confirmed') {
            $event->increment('registered');
        }

        return view('guest.guest-event-success', compact('event', 'registration'));
    }
}
