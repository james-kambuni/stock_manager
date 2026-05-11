<div class="card shadow rounded-3 mb-4">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-bag-plus-fill me-1"></i> New Purchase
    </div>

    <div class="card-body">

        {{-- ✅ ERROR DISPLAY --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('user.products.purchase') }}" method="POST" id="purchaseForm">
            @csrf

            <div class="row mb-3">

                {{-- PRODUCT --}}
                <div class="col-md-4">
                    <label><i class="bi bi-box-seam me-1"></i> Product</label>

                    <select class="form-select" name="product_id" id="productSelect" required>
                        <option value="">-- Select Product --</option>

                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                data-cost="{{ $product->cost_price }}"
                                data-perishable="{{ $product->is_perishable ? 1 : 0 }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- QUANTITY --}}
                <div class="col-md-4">
                    <label><i class="bi bi-123 me-1"></i> Quantity</label>
                    <input type="number"
                           name="quantity"
                           class="form-control"
                           required
                           min="1"
                           value="{{ old('quantity') }}">
                </div>

                {{-- COST --}}
                <div class="col-md-4">
                    <label><i class="bi bi-currency-dollar me-1"></i> Unit Cost</label>
                    <input type="number"
                           name="cost_price"
                           step="0.01"
                           class="form-control"
                           id="unitCost"
                           required
                           min="0"
                           value="{{ old('cost_price') }}">
                </div>

            </div>

            {{-- EXPIRY --}}
            <div class="mb-3">
                <label>
                    <i class="bi bi-calendar-event me-1"></i> Expiry Date
                    <span id="expiryRequired" class="text-danger d-none">(required)</span>
                </label>

                <input type="date"
                       name="expiry_date"
                       class="form-control"
                       id="expiryDate"
                       value="{{ old('expiry_date') }}">
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
        const isPerishable = selectedOption.getAttribute('data-perishable') == "1";

        unitCostInput.value = cost ?? '';

        if (isPerishable) {
            expiryDateInput.required = true;
            expiryLabel.classList.remove('d-none');
        } else {
            expiryDateInput.required = false;
            expiryLabel.classList.add('d-none');
        }
    });

    form.addEventListener('submit', function (e) {

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const isPerishable = selectedOption.getAttribute('data-perishable') == "1";

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

    if (alert && typeof bootstrap !== 'undefined') {
        let bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
}, 4000);
</script>
@endpush