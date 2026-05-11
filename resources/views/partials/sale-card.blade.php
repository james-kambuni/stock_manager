<div class="container mt-4">
    <div class="card shadow rounded-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-cart-check me-2"></i> Make a Sale
            </h5>
        </div>

        <div class="card-body">
            <form id="sale-form" method="POST" action="{{ route('user.products.sell.multiple') }}">
                @csrf

                {{-- Product Rows --}}
                <div id="product-rows">
                    <div class="row mb-3 sale-item">
                        <div class="col-md-5 col-12 mb-2">
                            <label>Product</label>
                            <select name="products[0][product_id]" class="form-select product-select" required>
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">
                                        {{ $product->name }} - Ksh {{ $product->unit_price }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-6 mb-2">
                            <label>Quantity</label>
                            <input type="number" name="products[0][quantity]" class="form-control quantity-input" min="1" required>
                        </div>

                        <div class="col-md-3 col-6 mb-2">
                            <label>Unit Price</label>
                            <input type="number" name="products[0][unit_price]" class="form-control unit-price-input" step="0.01" required>
                        </div>

                        <div class="col-md-1 col-12 d-flex align-items-end justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-product px-2">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Add Product --}}
                <div class="mb-3">
                    <button type="button" id="add-product" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i> Add Product
                    </button>
                </div>

                {{-- Payment Method --}}
                <div class="mb-3">
                    <label><i class="bi bi-credit-card me-1"></i> Payment Method</label>
                    <select id="payment-method" name="payment_method" class="form-select" required>
                        <option value="">-- Choose Payment Method --</option>
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                    </select>
                </div>

                {{-- Total --}}
                <div class="mb-3 text-end">
                    <h5>Total: Ksh <span id="total-amount">0.00</span></h5>
                </div>

                {{-- Submit --}}
                <div class="text-end">
                    <button type="submit" id="submit-sale" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check-circle me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    let rowIndex = 1;

    function bindRowEvents(row) {
        row.querySelector('.product-select').addEventListener('change', function () {
            const price = this.options[this.selectedIndex].getAttribute('data-price') || 0;
            row.querySelector('.unit-price-input').value = price;
            calculateTotalAmount();
        });

        row.querySelector('.quantity-input').addEventListener('input', calculateTotalAmount);
        row.querySelector('.unit-price-input').addEventListener('input', calculateTotalAmount);
    }

    bindRowEvents(document.querySelector('.sale-item'));

    document.getElementById('add-product').addEventListener('click', () => {
        const row = document.querySelector('.sale-item').cloneNode(true);
        row.querySelectorAll('input, select').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${rowIndex}]`);
            el.value = '';
        });
        document.getElementById('product-rows').appendChild(row);
        bindRowEvents(row);
        rowIndex++;
    });

    document.addEventListener('click', e => {
        if (e.target.closest('.remove-product')) {
            const rows = document.querySelectorAll('.sale-item');
            if (rows.length > 1) {
                e.target.closest('.sale-item').remove();
                calculateTotalAmount();
            } else {
                alert('At least one product is required.');
            }
        }
    });

    function calculateTotalAmount() {
        let total = 0;
        document.querySelectorAll('.sale-item').forEach(row => {
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            total += qty * price;
        });
        document.getElementById('total-amount').innerText = total.toFixed(2);
        return total.toFixed(2);
    }

    document.getElementById('submit-sale').addEventListener('click', function (e) {
        const method = document.getElementById('payment-method').value;
        const amount = calculateTotalAmount();

        if (!method) {
            e.preventDefault();
            return alert('Select payment method');
        }

        if (method === 'mpesa') {
            e.preventDefault();
            const phone = prompt('Enter customer phone number (07XXXXXXXX):');
            if (!/^07\d{8}$/.test(phone)) return alert('Invalid phone number.');

            fetch('{{ route("mpesa.stk.push") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone, amount })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('STK push sent. Waiting for confirmation...');
                    setTimeout(() => document.getElementById('sale-form').submit(), 7000);
                } else {
                    alert(data.message || 'M-Pesa error');
                }
            })
            .catch(() => alert('M-Pesa request failed.'));
        }
    });
});
</script>
