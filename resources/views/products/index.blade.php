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
        <label for="purchaseQuantity" class="form-label">Quantity Purchased</label>
        <input type="number" name="quantity" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="purchaseUnitCost" class="form-label">Unit Cost Price (Ksh)</label>
        <input type="number" step="0.01" class="form-control" id="purchaseUnitCost" name="cost_price" required>
    </div>

    <button type="submit" class="btn btn-info">Record Purchase</button>
</form>
<script>
    const products = @json($products);

    document.getElementById('purchaseProduct').addEventListener('change', function () {
        const selectedId = this.value;
        const product = products.find(p => p.id == selectedId);
        if (product) {
            document.getElementById('purchaseUnitCost').value = product.cost_price;
        } else {
            document.getElementById('purchaseUnitCost').value = '';
        }
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
            <form method="POST" action="{{ route('products.sell') }}">
                @csrf
                <div class="mb-3">
                    <label for="salesProduct" class="form-label">Product</label>
                    <select class="form-select" id="salesProduct" name="product_id" required>
                        <option value="">Select a product</option>
                        @foreach($products as $product)
                            <option 
                                value="{{ $product->id }}"
                                data-price="{{ $product->selling_price }}"
                            >
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="unitPrice" class="form-label">Unit Selling Price (Ksh)</label>
                    <input type="number" step="0.01" id="unitPrice" name="unit_price" class="form-control" readonly required>
                </div>

                <div class="mb-3">
                    <label for="quantitySold" class="form-label">Quantity Sold</label>
                    <input type="number" step="0.01" class="form-control" name="quantity" required>
                </div>

                <button type="submit" class="btn btn-success">Record Sale</button>
            </form>
            <script>
    document.getElementById('salesProduct').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        document.getElementById('unitPrice').value = price ?? '';
    });
</script>

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
