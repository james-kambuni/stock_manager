@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Create New Tenant</h1>

    <form method="POST" action="{{ route('master.tenant.store') }}">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input name="email" type="email" class="form-control" required>
        </div>
        <button class="btn btn-success">Create</button>
    </form>
</div>
@endsection
