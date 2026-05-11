@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width:450px;">
        <div class="card-body">

            <h3 class="mb-4 text-center">Reset Password</h3>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           value="{{ $email }}"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label>New Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-success w-100">
                    Reset Password
                </button>

            </form>

        </div>
    </div>
</div>

@endsection