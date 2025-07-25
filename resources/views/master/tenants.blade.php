@extends('layouts.master')

@section('content')
    <div class="container">
        <h1 class="mb-4">Tenants List</h1>

        @if($tenants->isEmpty())
            <p>No tenants found.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tenant ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenants as $index => $tenant)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $tenant->tenant_id }}</td>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->email }}</td>
                            <td>{{ $tenant->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('master.tenant.data', $tenant->tenant_id) }}" class="btn btn-sm btn-primary">
                                    View Tenant Data
                                </a>
                            </td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
