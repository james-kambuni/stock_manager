@extends('layouts.admin')
@section('title', 'Manage Users')
@section('content')

<div class="mb-3 text-end">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add User</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Registered</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at->diffForHumans() }}</td>
            <td>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info">Edit</a>

                <!-- Delete -->
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">No users found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
