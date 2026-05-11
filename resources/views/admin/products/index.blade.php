@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <h3 class="mb-3 text-primary">📦 Manage Products</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Add Product Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Add Product</div>
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <input type="number" step="0.01" name="stock" class="form-control" placeholder="Initial Stock" required>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <input type="number" step="0.01" name="cost_price" class="form-control" placeholder="Cost Price" required>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <input type="number" step="0.01" name="selling_price" class="form-control" placeholder="Selling Price" required>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <select name="is_perishable" class="form-select" required>
                            <option value="0">Non-Perishable</option>
                            <option value="1">Perishable</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <input type="number" name="min_threshold" class="form-control" placeholder="Min Threshold">
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <input type="number" name="max_threshold" class="form-control" placeholder="Max Threshold">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success w-100" type="submit">➕ Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark fw-bold">Product Inventory</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Stock</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Profit</th>
                            <th>Perishable</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        @php
                            $status = 'Sufficient';
                            $color = 'text-success';

                            if ($product->min_threshold !== null && $product->stock < $product->min_threshold) {
                                $status = 'Low';
                                $color = 'text-danger';
                            } elseif ($product->max_threshold !== null && $product->stock > $product->max_threshold) {
                                $status = 'Overstocked';
                                $color = 'text-warning';
                            }
                        @endphp
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>Ksh {{ number_format($product->cost_price, 2) }}</td>
                            <td>Ksh {{ number_format($product->selling_price, 2) }}</td>
                            <td>Ksh {{ number_format($product->selling_price - $product->cost_price, 2) }}</td>
                            <td>
                                <span class="badge {{ $product->is_perishable ? 'bg-danger' : 'bg-secondary' }}">
                                    {{ $product->is_perishable ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>{{ $product->min_threshold ?? '-' }}</td>
                            <td>{{ $product->max_threshold ?? '-' }}</td>
                            <td class="{{ $color }}">{{ $status }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete product?')">🗑</button>
                                </form>

                                <button class="btn btn-sm btn-warning edit-btn mt-1"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-stock="{{ $product->stock }}"
                                    data-cost="{{ $product->cost_price }}"
                                    data-price="{{ $product->selling_price }}"
                                    data-min="{{ $product->min_threshold }}"
                                    data-max="{{ $product->max_threshold }}"
                                    data-perishable="{{ $product->is_perishable }}"
                                    data-bs-toggle="modal" data-bs-target="#editProductModal">
                                    ✏️
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editProductForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editProductId">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" id="editName" name="name" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Stock</label>
                        <input type="number" step="0.01" class="form-control" id="editStock" name="stock">
                    </div>
                    <div class="mb-3">
                        <label>Cost Price</label>
                        <input type="number" step="0.01" class="form-control" id="editCost" name="cost_price">
                    </div>
                    <div class="mb-3">
                        <label>Selling Price</label>
                        <input type="number" step="0.01" class="form-control" id="editPrice" name="selling_price">
                    </div>
                    <div class="mb-3">
                        <label>Min Threshold</label>
                        <input type="number" class="form-control" id="editMin" name="min_threshold">
                    </div>
                    <div class="mb-3">
                        <label>Max Threshold</label>
                        <input type="number" class="form-control" id="editMax" name="max_threshold">
                    </div>
                    <div class="mb-3">
                        <label>Is Perishable?</label>
                        <select id="editPerishable" name="is_perishable" class="form-select">
                            <option value="0">Non-Perishable</option>
                            <option value="1">Perishable</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editBtns = document.querySelectorAll('.edit-btn');
        const form = document.getElementById('editProductForm');

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                document.getElementById('editProductId').value = id;
                document.getElementById('editName').value = btn.dataset.name;
                document.getElementById('editStock').value = btn.dataset.stock;
                document.getElementById('editCost').value = btn.dataset.cost;
                document.getElementById('editPrice').value = btn.dataset.price;
                document.getElementById('editMin').value = btn.dataset.min;
                document.getElementById('editMax').value = btn.dataset.max;
                document.getElementById('editPerishable').value = btn.dataset.perishable;

                form.action = `/admin/products/${id}`;
            });
        });
    });
</script>
<script>
    setTimeout(() => {
        let alert = document.querySelector('.alert');
        if (alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 4000);
</script>
@endsection
