@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')

<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
    </div>

    <button class="btn btn-primary">Update</button>
</form>

<hr>

<h5 class="mt-4">Change Password</h5>
<form method="POST" action="{{ route('admin.users.updatePassword', $user) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button class="btn btn-warning">Change Password</button>
</form>
@endsection
