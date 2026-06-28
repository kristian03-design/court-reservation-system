<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourtRequest;
use App\Models\Court;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourtController extends Controller
{
    public function index(): View
    {
        return view('admin.courts.admin-courts', [
            'courts' => Court::latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.courts.admin-court-form', ['court' => new Court()]);
    }

    public function store(StoreCourtRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('courts', 'public');
            $data['image'] = 'storage/' . $path;
        }

        unset($data['image_file']);

        $court = Court::create($data);

        \App\Services\AuditLogService::log('court_created', $court, [
            'court_name' => $court->court_name,
            'court_type' => $court->court_type,
        ]);

        return redirect()->route('admin.courts.index')->with('success', 'Court created.');
    }

    public function edit(Court $court): View
    {
        return view('admin.courts.admin-court-form', ['court' => $court]);
    }

    public function update(StoreCourtRequest $request, Court $court): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('courts', 'public');
            $data['image'] = 'storage/' . $path;
        }

        unset($data['image_file']);

        $court->update($data);

        \App\Services\AuditLogService::log('court_updated', $court, [
            'court_name' => $court->court_name,
            'court_type' => $court->court_type,
        ]);

        return redirect()->route('admin.courts.index')->with('success', 'Court updated.');
    }

    public function destroy(Court $court): RedirectResponse
    {
        $courtName = $court->court_name;
        $court->delete();

        \App\Services\AuditLogService::log('court_deleted', null, [
            'court_name' => $courtName,
        ]);

        return back()->with('success', 'Court deleted.');
    }
}
