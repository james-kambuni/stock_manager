@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width:450px;">
        <div class="card-body">

            <h3 class="mb-4 text-center">Forgot Password</h3>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label>Email Address</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-primary w-100">
                    Send Reset Link
                </button>
            </form>

        </div>
    </div>
</div>

@endsection