@extends('layouts.admin')

@section('title', 'Services')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Services</h5>

    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Add Service
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Price (Ksh)</th>
            <th>Status</th>
            <th width="150">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>{{ number_format($service->price, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $service->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($service->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-sm btn-warning">
                        Edit
                    </a>

                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted">No services found</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection