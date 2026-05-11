@extends('layouts.admin')

@section('title', 'Add Service')

@section('content')

<div class="card shadow p-4">
    <h5 class="mb-3">Add New Service</h5>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.services.store') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label>Service Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label>Price (Ksh)</label>
            <input type="number" name="price" class="form-control" step="0.01" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Save Service</button>
    </form>
</div>

@endsection