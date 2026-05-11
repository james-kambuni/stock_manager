@extends('layouts.admin')

@section('title', 'Edit Service')

@section('content')

<div class="card shadow p-4">
    <h5>Edit Service</h5>

    <form method="POST" action="{{ route('admin.services.update', $service->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Service Name</label>
            <input type="text" name="name" value="{{ $service->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" value="{{ $service->price }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" {{ $service->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $service->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $service->description }}</textarea>
        </div>

        <button class="btn btn-primary">Update Service</button>
    </form>
</div>

@endsection