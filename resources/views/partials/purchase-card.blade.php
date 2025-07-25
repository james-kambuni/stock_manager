<div class="card shadow rounded-3 mb-4">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-bag-plus-fill me-1"></i> New Purchase
    </div>
    <div class="card-body">
        <form action="{{ route('user.products.purchase') }}" method="POST" id="purchaseForm">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label><i class="bi bi-box-seam me-1"></i> Product</label>
                    <select class="form-select" name="product_id" id="productSelect" required>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                data-cost="{{ $product->cost_price }}"
                                data-perishable="{{ $product->is_perishable }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label><i class="bi bi-123 me-1"></i> Quantity</label>
                    <input type="number" name="quantity" class="form-control" required min="1">
                </div>
                <div class="col-md-4">
                    <label><i class="bi bi-currency-dollar me-1"></i> Unit Cost</label>
                    <input type="number" name="cost_price" step="0.01" class="form-control" id="unitCost" required min="0">
                </div>
            </div>
            <div class="mb-3">
                <label><i class="bi bi-calendar-event me-1"></i> Expiry Date <span id="expiryRequired" class="text-danger d-none">(required)</span></label>
                <input type="date" name="expiry_date" class="form-control" id="expiryDate">
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Submit Purchase
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productSelect = document.getElementById('productSelect');
        const unitCostInput = document.getElementById('unitCost');
        const expiryDateInput = document.getElementById('expiryDate');
        const expiryLabel = document.getElementById('expiryRequired');
        const form = document.getElementById('purchaseForm');

        productSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const cost = selectedOption.getAttribute('data-cost');
            const isPerishable = selectedOption.getAttribute('data-perishable') === '1';

            unitCostInput.value = cost ?? '';

            if (isPerishable) {
                expiryDateInput.required = true;
                expiryLabel.classList.remove('d-none');
            } else {
                expiryDateInput.required = false;
                expiryLabel.classList.add('d-none');
            }
        });

        // Optional: validate before submit
        form.addEventListener('submit', function (e) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const isPerishable = selectedOption.getAttribute('data-perishable') === '1';

            if (isPerishable && !expiryDateInput.value) {
                alert('Please provide an expiry date for perishable products.');
                e.preventDefault();
            }
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
@endpush
