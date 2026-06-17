<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'courts' => Court::available()->latest()->take(5)->get(),
            'testimonials' => Testimonial::where('is_featured', true)->take(3)->get(),
        ]);
    }
}
