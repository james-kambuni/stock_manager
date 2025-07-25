<div class="card shadow rounded-4 border-0 mb-4">
    <div class="card-header bg-success text-white fw-bold">
        <i class="bi bi-cart-check-fill me-1"></i> New Multi-Sale
    </div>
    <div class="card-body">
        <form id="sale-form" action="{{ route('user.products.sell.multiple') }}" method="POST">
            @csrf
            <div id="sale-items">
                <div class="row g-3 align-items-end border-bottom pb-3 mb-3 sale-item">
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-box-seam me-1"></i> Product</label>
                        <select name="products[0][product_id]" class="form-select product-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="bi bi-123 me-1"></i> Quantity</label>
                        <input type="number" name="products[0][quantity]" class="form-control" required min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="bi bi-currency-dollar me-1"></i> Unit Price</label>
                        <input type="number" name="products[0][unit_price]" class="form-control unit-price" step="0.01" required min="0">
                        <input type="hidden" class="default-price" name="products[0][default_price]">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-product" disabled>
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addSaleItem()">
                    <i class="bi bi-plus-circle me-1"></i> Add Product
                </button>

                <button type="submit" class="btn btn-success btn-sm rounded-pill px-4">
                    <i class="bi bi-check-circle me-1"></i> Submit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 1;

    function addSaleItem() {
        const container = document.getElementById('sale-items');

        const newRow = document.createElement('div');
        newRow.classList.add('row', 'g-3', 'align-items-end', 'border-bottom', 'pb-3', 'mb-3', 'sale-item');

        newRow.innerHTML = `
            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-box-seam me-1"></i> Product</label>
                <select name="products[${itemIndex}][product_id]" class="form-select product-select" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-123 me-1"></i> Quantity</label>
                <input type="number" name="products[${itemIndex}][quantity]" class="form-control" required min="1">
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-currency-dollar me-1"></i> Unit Price</label>
                <input type="number" name="products[${itemIndex}][unit_price]" class="form-control unit-price" step="0.01" required min="0">
                <input type="hidden" class="default-price" name="products[${itemIndex}][default_price]">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-product">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;

        container.appendChild(newRow);
        itemIndex++;
    }

    // Autofill price when product is selected
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const selected = e.target.selectedOptions[0];
            const unitPrice = selected.getAttribute('data-price');
            const row = e.target.closest('.sale-item');
            const priceInput = row.querySelector('.unit-price');
            const defaultInput = row.querySelector('.default-price');

            if (unitPrice) {
                priceInput.value = parseFloat(unitPrice).toFixed(2);
                defaultInput.value = parseFloat(unitPrice).toFixed(2);
            } else {
                priceInput.value = '';
                defaultInput.value = '';
            }
        }
    });

    // Remove row
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-product')) {
            const allRows = document.querySelectorAll('.sale-item');
            if (allRows.length > 1) {
                e.target.closest('.sale-item').remove();
            }
        }
    });

    // Confirm discount before submit
    document.getElementById('sale-form').addEventListener('submit', function (e) {
        const rows = document.querySelectorAll('.sale-item');
        let changes = [];

        rows.forEach(row => {
            const priceInput = row.querySelector('.unit-price');
            const defaultInput = row.querySelector('.default-price');

            const entered = parseFloat(priceInput.value);
            const original = parseFloat(defaultInput.value);

            if (!isNaN(entered) && !isNaN(original) && entered !== original) {
                const productName = row.querySelector('.product-select').selectedOptions[0]?.textContent.trim() || 'Unknown Product';
                changes.push(`${productName}: KES ${original.toFixed(2)} → KES ${entered.toFixed(2)}`);
            }
        });

        if (changes.length > 0) {
            const message = `The following products have a modified price:\n\n${changes.join('\n')}\n\nDo you want to proceed with the discount?`;
            if (!confirm(message)) {
                e.preventDefault();
            }
        }
    });
</script>
@endpush
