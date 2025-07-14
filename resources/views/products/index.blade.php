@extends('layouts.users')

@section('title', 'Manage Products')

@section('content')
<div class="row">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Record Purchases -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Record Purchases
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.purchase') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="purchaseProduct" class="form-label">Product</label>
                        <select name="product_id" id="purchaseProduct" class="form-select" required>
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity Purchased</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unit Cost Price (Ksh)</label>
                        <input type="number" step="0.01" class="form-control" id="purchaseUnitCost" name="cost_price" required>
                    </div>

                    <button type="submit" class="btn btn-info">Record Purchase</button>
                </form>

                <script>
                    const products = @json($products);
                    document.getElementById('purchaseProduct').addEventListener('change', function () {
                        const selectedId = this.value;
                        const product = products.find(p => p.id == selectedId);
                        document.getElementById('purchaseUnitCost').value = product ? product.cost_price : '';
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- Record Sales -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                Record Sales
            </div>
            <div class="card-body">
                @if ($errors->has('products.*'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->get('products.*') as $fieldErrors)
                                @foreach ($fieldErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.sell.multiple') }}">
                    @csrf
                    <div id="productSalesList">
                        <div class="product-group mb-3 border p-2 rounded">
                            <div class="row g-2">
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
                                    <label>Price (Editable)</label>
                                    <input type="number" step="0.01" class="form-control price" name="products[0][unit_price]" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control" name="products[0][quantity]" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-row d-none">Remove</button>
                        </div>
                    </div>

                    <button type="button" id="addProductRow" class="btn btn-sm btn-secondary my-2">Add Another Product</button>
                    <button type="submit" class="btn btn-success">Submit Sale & Print</button>
                </form>

                <script>
let productIndex = 1;

document.getElementById('addProductRow').addEventListener('click', function () {
    const container = document.getElementById('productSalesList');
    const original = container.querySelector('.product-group');
    const clone = original.cloneNode(true);

    clone.querySelectorAll('select, input').forEach(el => {
        const name = el.getAttribute('name');
        if (name) {
            el.setAttribute('name', name.replace(/\[\d+\]/, `[${productIndex}]`));
            el.value = '';
        }
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
        const price = parseFloat(selected.dataset.price);
        const parentGroup = e.target.closest('.product-group');
        const priceInput = parentGroup.querySelector('.price');

        priceInput.value = price.toFixed(2); // Set price from selected product
        priceInput.setAttribute('data-original-price', price); // Save original price
    }
});

// Restrict editing price to max 14% discount
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('price')) {
        const priceInput = e.target;
        const originalPrice = parseFloat(priceInput.getAttribute('data-original-price') || 0);
        const currentPrice = parseFloat(priceInput.value);

        if (!priceInput.value) return; // allow blank entry

        if (originalPrice && currentPrice < originalPrice * 0.86) {
            alert("You cannot apply more than 14% discount.");
            priceInput.value = originalPrice.toFixed(2); // Revert to original
        }
    }
});
</script>

            </div>
        </div>
    </div>
</div>

<!-- Reports Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Reports
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('user.reports.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" name="type">
                                <option value="sales">Sales Report</option>
                                <option value="purchases">Purchases Report</option>
                                <option value="inventory">Inventory Report</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary w-100">Generate Report</button>
                        </div>
                    </div>
                </form>

                <div id="reportResults" class="mt-3">
                    <p>Select report type and date range to view data.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
