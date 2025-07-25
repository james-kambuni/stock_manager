@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Manage Products</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Add Product Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Add Product</div>
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" name="stock" class="form-control" placeholder="Initial Stock" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" name="cost_price" class="form-control" placeholder="Cost Price" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" name="selling_price" class="form-control" placeholder="Selling Price" required>
                    </div>
                    <div class="col">
                        <select name="is_perishable" class="form-select" required>
                            <option value="0">Non-Perishable</option>
                            <option value="1">Perishable</option>
                        </select>
                    </div>
                    <div class="col">
                        <input type="number" name="min_threshold" class="form-control" placeholder="Min Threshold">
                    </div>
                    <div class="col">
                        <input type="number" name="max_threshold" class="form-control" placeholder="Max Threshold">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" type="submit">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card">
        <div class="card-header bg-warning">Product Inventory</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered m-0">
                    <thead>
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
                            <th>Actions</th>
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
                                @if($product->is_perishable)
                                    <span class="badge bg-danger">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>{{ $product->min_threshold ?? '-' }}</td>
                            <td>{{ $product->max_threshold ?? '-' }}</td>
                            <td class="{{ $color }}">{{ $status }}</td>
                            <td>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete product?')">Delete</button>
                                </form>

                                <button class="btn btn-sm btn-warning edit-btn"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-stock="{{ $product->stock }}"
                                    data-cost="{{ $product->cost_price }}"
                                    data-price="{{ $product->selling_price }}"
                                    data-min="{{ $product->min_threshold }}"
                                    data-max="{{ $product->max_threshold }}"
                                    data-perishable="{{ $product->is_perishable }}"
                                    data-bs-toggle="modal" data-bs-target="#editProductModal">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

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
                </div> <!-- End Modal -->

            </div>
        </div>
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
@endsection
