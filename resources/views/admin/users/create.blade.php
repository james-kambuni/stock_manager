@extends('layouts.admin')
@section('title', 'Add User')
@section('content')

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button class="btn btn-success">Create User</button>
</form>
@endsection
