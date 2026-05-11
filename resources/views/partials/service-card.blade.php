<!-- ================= RECORD SERVICE SALE ================= -->
<div class="card shadow rounded-3 mb-4">
    <div class="card-header bg-info text-white fw-bold">
        <i class="bi bi-cash-coin me-2"></i> Record Service Sale
    </div>

    <div class="card-body bg-light">
        <form action="{{ route('user.service-sales.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <!-- SERVICE SELECT -->
                <div class="col-md-5">
                    <label class="form-label">Select Service</label>
                    <select name="service_id" id="service_id" class="form-select" required>
                        <option value="">-- Choose Service --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                {{ $service->name }} (Ksh {{ $service->price }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- QUANTITY -->
                <div class="col-md-2">
                    <label class="form-label">Qty</label>
                    <input type="number" name="quantity" id="quantity"
                        class="form-control" value="1" min="1" required>
                </div>

                <!-- UNIT PRICE (EDITABLE) -->
                <div class="col-md-2">
                    <label class="form-label">Unit Price</label>
                    <input type="number" name="unit_price" id="unit_price"
                        class="form-control" step="0.01" required>
                </div>

                <!-- TOTAL AMOUNT -->
                <div class="col-md-3">
                    <label class="form-label">Total Amount</label>
                    <input type="number" name="amount" id="amount"
                        class="form-control" readonly required>
                </div>

                <!-- DATE -->
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date"
                        value="{{ date('Y-m-d') }}" class="form-control">
                </div>

            </div>

            <button type="submit" class="btn btn-info mt-3">
                Save Record
            </button>
        </form>
    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const serviceSelect = document.getElementById('service_id');
    const qtyInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const amountInput = document.getElementById('amount');

    function calculateTotal() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];

        const defaultPrice = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
        const qty = parseFloat(qtyInput.value) || 1;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;

        // Auto-fill unit price if service changes
        if (!unitPriceInput.dataset.manual) {
            unitPriceInput.value = defaultPrice;
        }

        amountInput.value = (parseFloat(unitPriceInput.value) || 0) * qty;
    }

    // When service changes → reset manual override
    serviceSelect.addEventListener('change', function () {
        unitPriceInput.dataset.manual = "";
        calculateTotal();
    });

    // When user edits unit price → mark manual
    unitPriceInput.addEventListener('input', function () {
        unitPriceInput.dataset.manual = "1";
        calculateTotal();
    });

    qtyInput.addEventListener('input', calculateTotal);

    // initialize
    calculateTotal();
});
</script>