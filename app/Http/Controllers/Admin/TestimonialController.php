<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->paginate(20);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function toggle(Testimonial $testimonial)
    {
        $testimonial->update(['is_featured' => ! $testimonial->is_featured]);

        $status = $testimonial->is_featured ? 'published on homepage' : 'hidden from homepage';

        return back()->with('success', "Testimonial from \"{$testimonial->name}\" {$status}.");
    }

    public function destroy(Testimonial $testimonial)
    {
        $name = $testimonial->name;
        $testimonial->delete();

        return back()->with('success', "Testimonial from \"{$name}\" deleted.");
    }
}
