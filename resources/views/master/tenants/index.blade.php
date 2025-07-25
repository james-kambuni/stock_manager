@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Tenants</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session()->has('impersonate'))
        <form action="{{ route('master.tenant.stopImpersonate') }}" method="POST">
            @csrf
            <button class="btn btn-danger">Stop Impersonation</button>
        </form>
    @endif

    <a href="{{ route('master.tenant.create') }}" class="btn btn-primary mb-3">Add Tenant</a>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Tenant ID</th>
                <th>Logo</th> 
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $index => $tenant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tenant->tenant_id }}</td>
                    <td>
                        @if($tenant->logo)
                            <img src="{{ asset('storage/logos/' . $tenant->logo) }}" alt="Logo" width="60" height="60">
                        @else
                            <span class="text-muted">No Logo</span>
                        @endif
                    </td>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->email }}</td>
                    <td>{{ $tenant->phone }}</td>
                    <td>
                        <span class="badge {{ $tenant->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $tenant->is_active ? 'Active' : 'Paused' }}
                        </span>
                    </td>
                    <td>
                        <!-- Toggle Status -->
                        <form action="{{ route('master.tenant.toggle', $tenant->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-warning" title="Toggle Status">
                                {{ $tenant->is_active ? 'Pause' : 'Activate' }}
                            </button>
                        </form>

                        <!-- Delete Tenant -->
                        <form action="{{ route('master.tenant.destroy', $tenant->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this tenant?')">
                                Delete
                            </button>
                        </form>

                        <!-- View Tenant Data -->
                        <a href="{{ route('master.tenant.data', $tenant->id) }}" class="btn btn-sm btn-info">
                            View Data
                        </a>

                        <!-- Impersonate -->
                        <form action="{{ route('master.tenant.impersonate', $tenant->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-secondary" title="Impersonate">
                                Impersonate
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No tenants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
