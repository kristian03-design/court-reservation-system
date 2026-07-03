<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Court;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('sport', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sport Filter
        if ($request->filled('sport')) {
            $query->where('sport', $request->sport);
        }

        $events = $query->latest()->paginate(10);

        // Stats
        $stats = [
            'total_events' => Event::count(),
            'total_registrants' => EventRegistration::count(),
            'total_revenue' => EventRegistration::where('payment_status', 'paid')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->sum('events.price'),
            'active_events' => Event::where('status', 'open')->count(),
        ];

        return view('admin.events.index', compact('events', 'stats'));
    }

    public function create(): View
    {
        $courts = Court::all();
        return view('admin.events.create', compact('courts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sport' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_slots' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'required|string|max:255',
            'court_id' => 'nullable|exists:courts,id',
            'image' => 'nullable|image|max:4096',
            'requires_payment' => 'boolean',
            'allow_waitlist' => 'boolean',
            'featured' => 'boolean',
            'published' => 'boolean',
            'status' => 'required|in:draft,open,closed,completed,cancelled',
        ]);

        $validated['requires_payment'] = $request->has('requires_payment');
        $validated['allow_waitlist'] = $request->has('allow_waitlist');
        $validated['featured'] = $request->has('featured');
        $validated['published'] = $request->has('published');

        // Automatically slugify title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $validated['slug'] = $slug;

        // Image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadEventImage($request->file('image'));
        }

        $event = Event::create($validated);

        if ($event->published) {
            $this->notifyUsersAboutEvent($event);
        }

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event): View
    {
        $courts = Court::all();
        return view('admin.events.edit', compact('event', 'courts'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sport' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_slots' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'required|string|max:255',
            'court_id' => 'nullable|exists:courts,id',
            'image' => 'nullable|image|max:4096',
            'requires_payment' => 'boolean',
            'allow_waitlist' => 'boolean',
            'featured' => 'boolean',
            'published' => 'boolean',
            'status' => 'required|in:draft,open,closed,completed,cancelled',
        ]);

        $validated['requires_payment'] = $request->has('requires_payment');
        $validated['allow_waitlist'] = $request->has('allow_waitlist');
        $validated['featured'] = $request->has('featured');
        $validated['published'] = $request->has('published');

        if ($event->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $count = 1;
            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadEventImage($request->file('image'), $event->image);
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->image && file_exists(public_path($event->image))) {
            @unlink(public_path($event->image));
        }
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }

    public function duplicate(Event $event): RedirectResponse
    {
        $clone = $event->replicate();
        $clone->registered = 0;
        $clone->status = 'draft';
        $clone->published = false;

        $slug = $event->slug . '-copy';
        $originalSlug = $slug;
        $count = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $clone->slug = $slug;
        $clone->title = $event->title . ' (Copy)';
        $clone->save();

        return redirect()->route('admin.events.index')->with('success', 'Event duplicated successfully as draft.');
    }

    public function togglePublish(Event $event): RedirectResponse
    {
        $event->update([
            'published' => !$event->published,
            'status' => !$event->published ? 'open' : 'draft',
        ]);

        if ($event->published) {
            $this->notifyUsersAboutEvent($event);
        }

        $status = $event->published ? 'published' : 'unpublished';
        return back()->with('success', "Event has been {$status}.");
    }

    public function registrants(Event $event): View
    {
        $registrations = $event->registrations()->with('user')->latest()->paginate(15);
        return view('admin.events.registrants', compact('event', 'registrations'));
    }

    public function updateRegistrationStatus(Request $request, EventRegistration $registration): RedirectResponse
    {
        $request->validate([
            'registration_status' => 'required|in:pending,confirmed,waitlisted,cancelled',
        ]);

        $registration->update([
            'registration_status' => $request->registration_status,
        ]);

        // Recalculate event registered count
        $event = $registration->event;
        $event->update([
            'registered' => $event->registrations()->where('registration_status', 'confirmed')->count(),
        ]);

        return back()->with('success', 'Registration status updated.');
    }

    public function updatePaymentStatus(Request $request, EventRegistration $registration): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,pending_verification,paid',
        ]);

        $registration->update([
            'payment_status' => $request->payment_status,
        ]);

        if ($request->payment_status === 'paid') {
            $registration->update([
                'registration_status' => 'confirmed',
            ]);
            
            $event = $registration->event;
            $event->update([
                'registered' => $event->registrations()->where('registration_status', 'confirmed')->count(),
            ]);
        }

        return back()->with('success', 'Payment status updated.');
    }

    private function notifyUsersAboutEvent(Event $event): void
    {
        $users = \App\Models\User::where('role', 'customer')->get();
        $startDateFormatted = $event->start_date
            ? (\Carbon\Carbon::parse($event->start_date)->format('M d, Y'))
            : 'TBA';
        foreach ($users as $user) {
            $user->systemNotifications()->create([
                'title' => 'New Event: ' . $event->title,
                'message' => "An exciting new {$event->sport} session ({$event->event_type}) is now open for registration! Join us on " . $startDateFormatted . '.',
                'is_read' => false,
            ]);
        }
    }

    /**
     * Upload an event image.
     * On Vercel (read-only filesystem), uploads to Supabase Storage via S3-compatible API.
     * In local dev (no S3 credentials), stores to local public storage.
     *
     * @param  UploadedFile  $file
     * @param  string|null   $oldImagePath  Path of the old image to delete (optional)
     * @return string  The stored image path/URL to save in the database
     */
    private function uploadEventImage(UploadedFile $file, ?string $oldImagePath = null): string
    {
        $s3Key    = config('services.supabase.s3_key');
        $s3Secret = config('services.supabase.s3_secret');
        $s3Url    = config('services.supabase.s3_endpoint');
        $bucket   = config('services.supabase.bucket', 'courtconnect-uploads');

        $ext      = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
        $s3Path   = 'events/' . $fileName;

        if ($s3Key && $s3Secret && $s3Url) {
            // --- Supabase S3-compatible Storage (production / Vercel) ---
            config([
                'filesystems.disks.supabase_s3.driver'                  => 's3',
                'filesystems.disks.supabase_s3.key'                     => $s3Key,
                'filesystems.disks.supabase_s3.secret'                  => $s3Secret,
                'filesystems.disks.supabase_s3.region'                  => config('services.supabase.s3_region', 'ap-southeast-1'),
                'filesystems.disks.supabase_s3.bucket'                  => $bucket,
                'filesystems.disks.supabase_s3.endpoint'                => $s3Url,
                'filesystems.disks.supabase_s3.use_path_style_endpoint' => true,
                'filesystems.disks.supabase_s3.throw'                   => false,
            ]);

            $disk = Storage::disk('supabase_s3');

            // Delete old file if it was previously in Supabase
            if ($oldImagePath && Str::startsWith($oldImagePath, 'https://')) {
                $oldKey = preg_replace('#^.*/events/#', 'events/', $oldImagePath);
                try { $disk->delete($oldKey); } catch (\Throwable) {}
            }

            $uploaded = $disk->put($s3Path, file_get_contents($file->getRealPath()), 'public');

            if ($uploaded) {
                $supabasePublicUrl = config('services.supabase.url');
                return "{$supabasePublicUrl}/storage/v1/object/public/{$bucket}/{$s3Path}";
            }
        }

        // --- Local storage fallback (development) ---
        if ($oldImagePath && !Str::startsWith($oldImagePath, 'https://')) {
            $oldLocal = ltrim(str_replace('storage/', '', $oldImagePath), '/');
            Storage::disk('public')->delete($oldLocal);
        }
        $path = $file->storeAs('events', $fileName, 'public');
        return 'storage/' . $path;
    }
}
