@extends('layouts.admin')

@section('title', 'Record Sales and Purchases')

@section('content')
{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger shadow-sm">
        <h5 class="alert-heading">Please fix the following:</h5>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    {{-- Record Purchases --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-info">
            <div class="card-header bg-info text-white d-flex align-items-center">
                <i class="bi bi-box-arrow-in-down me-2"></i>
                <strong>Record New Purchase</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.transactions.storePurchase') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="purchase_product" class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="purchase_product" class="form-select" required>
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-cost="{{ $product->cost_price }}"
                                    data-perishable="{{ $product->is_perishable }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Cost</label>
                            <input type="number" step="0.01" id="purchaseUnitCost" name="unit_cost" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="expiry_date" class="form-label">Expiry Date <small id="expiryLabelNote">(optional)</small></label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-info w-100">
                        <i class="bi bi-save me-1"></i> Save Purchase
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Record Sales --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-success">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="bi bi-cash-coin me-2"></i>
                <strong>Record Sales</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.transactions.storeSaleMultiple') }}">
                    @csrf
                    <div id="productSalesList">
                        <div class="product-group border p-3 rounded bg-light mb-3 shadow-sm">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label>Product</label>
                                    <select name="products[0][product_id]" class="form-select product-select" required>
                                        <option value="">Select a product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Unit Price</label>
                                    <input type="number" step="0.01" name="products[0][unit_price]" class="form-control price" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Quantity</label>
                                    <input type="number" name="products[0][quantity]" class="form-control" required>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row d-none">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="button" id="addProductRow" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Add Another
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-cart-check me-1"></i> Submit Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Bootstrap Icons CDN if not already included --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

{{-- JavaScript --}}
<script>
let productIndex = 1;

document.getElementById('purchase_product').addEventListener('change', function () {
    const selected = this.selectedOptions[0];
    const cost = selected.getAttribute('data-cost');
    const isPerishable = selected.getAttribute('data-perishable') === '1';

    document.getElementById('purchaseUnitCost').value = cost || '';

    const expiry = document.getElementById('expiry_date');
    const labelNote = document.getElementById('expiryLabelNote');

    expiry.required = isPerishable;
    labelNote.textContent = isPerishable ? '(required)' : '(optional)';
    if (!isPerishable) expiry.value = '';
});

document.getElementById('addProductRow').addEventListener('click', function () {
    const container = document.getElementById('productSalesList');
    const original = container.querySelector('.product-group');
    const clone = original.cloneNode(true);

    clone.querySelectorAll('select, input').forEach(el => {
        const name = el.getAttribute('name');
        if (name) el.setAttribute('name', name.replace(/\[\d+\]/, `[${productIndex}]`));
        el.value = '';
    });

    clone.querySelector('.remove-row').classList.remove('d-none');
    container.appendChild(clone);
    productIndex++;
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('.product-group').remove();
    }
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('product-select')) {
        const selected = e.target.selectedOptions[0];
        const price = selected.getAttribute('data-price');
        const group = e.target.closest('.product-group');
        const input = group.querySelector('.price');
        input.value = price || '';
        input.setAttribute('data-original-price', price);
    }
});

document.addEventListener('blur', function (e) {
    if (e.target.classList.contains('price')) {
        const input = e.target;
        const value = parseFloat(input.value);
        const original = parseFloat(input.getAttribute('data-original-price'));
        if (!input.value || isNaN(value)) return;
        if (value < original * 0.86) {
            alert('Discount cannot exceed 14%.');
            input.value = original.toFixed(2);
        }
    }
}, true);
</script>
@endsection
