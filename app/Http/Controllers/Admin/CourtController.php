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
        return view('admin.courts.index', [
            'courts' => Court::latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.courts.form', ['court' => new Court()]);
    }

    public function store(StoreCourtRequest $request): RedirectResponse
    {
        Court::create($request->validated());

        return redirect()->route('admin.courts.index')->with('success', 'Court created.');
    }

    public function edit(Court $court): View
    {
        return view('admin.courts.form', ['court' => $court]);
    }

    public function update(StoreCourtRequest $request, Court $court): RedirectResponse
    {
        $court->update($request->validated());

        return redirect()->route('admin.courts.index')->with('success', 'Court updated.');
    }

    public function destroy(Court $court): RedirectResponse
    {
        $court->delete();

        return back()->with('success', 'Court deleted.');
    }
}
