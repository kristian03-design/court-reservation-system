<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilitySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = \Illuminate\Support\Facades\Cache::rememberForever('facility_settings', function () {
            return FacilitySetting::pluck('value', 'key');
        });

        return view('admin.settings.admin-settings', [
            'settings' => $settings,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'facility_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'operating_hours' => ['required', 'string', 'max:255'],
            'reservation_rules' => ['nullable', 'string'],
        ]);

        foreach ($data as $key => $value) {
            FacilitySetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        \Illuminate\Support\Facades\Cache::forget('facility_settings');

        return back()->with('success', 'Settings saved.');
    }
}
