<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.users.admin-users', [
            'users' => User::query()
                ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,customer'],
            'status' => ['required', 'in:active,disabled'],
        ]);

        $user->update($data);

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->update(['status' => 'disabled']);

        return back()->with('success', 'User disabled.');
    }
}
