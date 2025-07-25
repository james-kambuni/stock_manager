@extends('layouts.admin')

@section('title', 'Stock Reconciliation')

@section('content')
<div class="container mt-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.stock-reconciliation.export') }}" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf"></i> Export to PDF
        </a>
    </div>

    {{-- Reconciliation Form --}}
    <form method="POST" action="{{ route('admin.stock-reconciliation.store') }}">
        @csrf

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white fw-semibold">
                Compare Physical vs System Stock
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 30%">Product</th>
                                <th style="width: 20%">System Stock</th>
                                <th style="width: 25%">Physical Stock</th>
                                <th style="width: 25%">Discrepancy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php
                                    $physical = old("counts.{$product->id}") ?? ($product->stockCount->physical_stock ?? 0);
                                    $discrepancy = $physical - $product->stock;
                                @endphp
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-center">{{ $product->stock }}</td>
                                    <td class="text-center">
                                        <input type="number"
                                               name="counts[{{ $product->id }}]"
                                               value="{{ $physical }}"
                                               class="form-control text-center"
                                               min="0"
                                               required>
                                    </td>
                                    <td class="text-center fw-bold {{ $discrepancy == 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $discrepancy }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                {{-- Submit Reconciliation --}}
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-check-circle"></i> Submit Reconciliation
                </button>

                {{-- Update System Stock button will trigger external form --}}
                <button type="button" class="btn btn-warning px-4" onclick="confirmUpdateStock()">
                    <i class="fas fa-sync-alt"></i> Update System Stock
                </button>
            </div>
        </div>
    </form>

    {{-- External form for system stock update --}}
    <form id="updateSystemStockForm" method="POST" action="{{ route('admin.stock-reconciliation.updateSystemStock') }}" style="display:none;">
        @csrf
        <input type="hidden" name="counts_json" id="counts_json">
    </form>
</div>
@endsection

@push('scripts')
<script>
    function confirmUpdateStock() {
        if (confirm('Are you sure you have physically audited the stock? This will overwrite system stock values.')) {
            const counts = {};
            document.querySelectorAll('input[name^="counts"]').forEach(input => {
                const idMatch = input.name.match(/counts\[(\d+)\]/);
                if (idMatch) {
                    counts[idMatch[1]] = input.value;
                }
            });
            document.getElementById('counts_json').value = JSON.stringify(counts);
            document.getElementById('updateSystemStockForm').submit();
        }
    }
</script>
@endpush
