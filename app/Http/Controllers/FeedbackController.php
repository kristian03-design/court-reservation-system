<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Show the player feedback form and their existing feedback.
     */
    public function index()
    {
        $user = Auth::user();
        $myFeedback = Testimonial::where('user_id', $user->id)->latest()->get();

        return view('user.feedback.index', compact('myFeedback'));
    }

    /**
     * Store new feedback submitted by the player.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $user = Auth::user();

        // One testimonial per user cap — update existing or create new
        Testimonial::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'        => $user->name,
                'rating'      => $validated['rating'],
                'comment'     => $validated['comment'],
                'is_featured' => true, // auto-published immediately
            ]
        );

        return redirect()->route('feedback.index')
            ->with('success', 'Thank you! Your feedback has been saved.');
    }

    /**
     * Delete a player's own feedback.
     */
    public function destroy()
    {
        $user = Auth::user();
        Testimonial::where('user_id', $user->id)->delete();

        return redirect()->route('feedback.index')
            ->with('success', 'Your feedback has been removed.');
    }
}
