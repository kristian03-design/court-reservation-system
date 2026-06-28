@extends('admin.layouts.shell', ['pageTitle' => 'Users', 'active' => 'users'])

@section('content')
    <div class="page-header">
        <div>
            <p class="page-eyebrow">Access Management</p>
            <h1>Users</h1>
        </div>
    </div>

    <section class="mt-6">
        <div class="table-wrap">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="font-semibold" data-label="Name">{{ $user->name }}</td>
                                <td data-label="Email">{{ $user->email }}</td>
                                <td data-label="Role"><span class="status status-{{ $user->status === 'active' ? 'active' : 'pending' }}">{{ $user->role }} - {{ $user->status ?? 'active' }}</span></td>
                                <td data-label="Joined Date">{{ $user->created_at->format('M d, Y') }}</td>
                                <td style="text-align: right;" data-label="Action">
                                    @if($user->role !== 'admin')
                                        <form action="{{ route('admin.users.update', $user) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="role" value="{{ $user->role }}">
                                            <input type="hidden" name="status" value="{{ ($user->status ?? 'active') === 'active' ? 'disabled' : 'active' }}">
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                {{ ($user->status ?? 'active') === 'active' ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;padding:32px;">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div style="padding: 16px; border-top: 1px solid var(--border);">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
