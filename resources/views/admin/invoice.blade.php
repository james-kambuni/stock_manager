@extends('layouts.admin')

@section('title', 'Invoice Generator')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #invoice .row p,
        #invoice .row h6,
        #bank-details p,
        #bank-details h6,
        #terms p,
        #terms h6 {
            margin: 1px 0 !important;
            line-height: 1.1 !important;
            font-size: 9px !important;
        }


        #invoice, #invoice * {
            visibility: visible;
        }

        #invoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            font-size: 10px;
            padding: 10px;
        }

        #invoice table {
            font-size: 9px;
            margin-bottom: 0;
        }

        #invoice table th,
        #invoice table td {
            padding: 2px 4px;
        }

        .no-print {
            display: none !important;
        }

        #invoice .invoice-body {
            page-break-inside: avoid;
        }
    }

    #invoice {
        background: white;
        padding: 10px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
        font-size: 11px;
        min-height: 100vh;
    }

    #invoice table th,
    #invoice table td {
        font-size: 10px;
        padding: 3px 5px;
        vertical-align: middle;
    }

    #invoice-header img {
        max-width: 90px;
    }

    #invoice-header h3 {
        font-size: 14px;
        margin: 2px 0;
    }

    #invoice-header p {
        font-size: 10px;
        margin: 0;
    }

    #bank-details,
    #terms {
        font-size: 9px;
        margin-top: 10px;
    }

    #bank-details h6,
    #terms h6 {
        font-size: 10px;
        margin-bottom: 5px;
    }

    .form-control-sm {
        font-size: 11px;
        padding: 3px 6px;
    }

    .table-bordered td, 
    .table-bordered th {
        border: 1px solid #dee2e6 !important;
    }

    .table th, 
    .table td {
        white-space: nowrap;
    }
</style>


@endpush

@section('content')
<div class="container my-4">
    <h2 class="mb-4 text-center">Invoice Generation</h2>

    <div class="mb-3 no-print text-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#customerModal">
            👤 Customer Details
        </button>
        <button type="button" class="btn btn-outline-primary me-2" onclick="window.print()">
            🖨️ Print Invoice
        </button>
        <button type="button" class="btn btn-outline-danger" id="downloadPdf">
            📄 Download PDF
        </button>
    </div>

    <!-- Product Entry -->
    <div class="card p-3 mb-4 no-print">
        <h5>Add Product to Invoice</h5>
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="productSelect" class="form-label">Select Product</label>
                <select class="form-select" id="productSelect" required>
                    <option value="">Select a product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->name }}" data-price="{{ $product->selling_price }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="productQty" class="form-label">Quantity</label>
                <input type="number" id="productQty" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="productPrice" class="form-label">Unit Price (Ksh)</label>
                <input type="number" id="productPrice" class="form-control" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" id="addProduct" class="btn btn-success w-100">Add</button>
            </div>
        </div>
    </div>

    <!-- Invoice -->
    <div id="invoice">
        <div class="invoice-body">
            <div id="invoice-header" class="text-center mb-4">
                @php
                    $tenant = auth()->user()->tenant;
                @endphp

                @if ($tenant && $tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }} Logo" style="max-height: 80px;">
                @endif

                <h3>Junik Drip Irrigation & Consultants</h3>
                <h3>Festus Building, Mwingi</h3>
                <h3>Pin: A011970484C</h3>
                <p><em>0758878628</em></p>
            </div>

            <div class="row mb-2">
                <div class="col-6" style="font-size: 0.75rem;">
                    <h6>Customer Details</h6>
                    <p><strong>Name:</strong> <span id="customerName">-</span></p>
                    <p><strong>Address:</strong> <span id="customerAddress">-</span></p>
                    <p><strong>Email:</strong> <span id="customerEmail">-</span></p>
                    <p><strong>Phone:</strong> <span id="customerPhone">-</span></p>
                </div>
                <div class="col-6 text-end" style="font-size: 0.75rem;">
                    <h6>Served By</h6>
                    <p><strong>Staff:</strong> <span id="servedBy">{{ $user->name }}</span></p>
                    <p><strong>Date:</strong> <span id="invoiceDate">{{ now()->format('d M Y') }}</span></p>
                    <p><strong>Invoice #:</strong> <span id="invoiceNumber">{{ $invoiceNumber }}</span></p>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="invoiceItems"></tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Subtotal</th>
                        <th id="subtotalAmount">0.00</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">VAT (16%)</th>
                        <th id="vatAmount">0.00</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th id="totalAmount">0.00</th>
                    </tr>
                </tfoot>
            </table>

            <div id="bank-details">
                <h6>Payment Details:</h6>
                <p>Paybill (KCB)</p>
                <p>Business NO: 522533</p>
                <p>Acc NO: 7933227</p>
                <p>Name: JUNK DRIP</p>
            </div>

            <div id="terms">
                <h6>Terms & Conditions:</h6>
                <p>Payment is due within 30 days from the invoice date. Late payments may be subject to interest charges.</p>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="customerForm">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputCustomerName" class="form-label">Customer Name</label>
                        <input type="text" id="inputCustomerName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputCustomerAddress" class="form-label">Address</label>
                        <input type="text" id="inputCustomerAddress" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputCustomerPhone" class="form-label">Phone Number</label>
                        <input type="text" id="inputCustomerPhone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputCustomerEmail" class="form-label">Email (optional)</label>
                        <input type="email" id="inputCustomerEmail" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Details</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const invoiceDateEl = document.getElementById('invoiceDate');
    if (invoiceDateEl) {
        invoiceDateEl.textContent = new Date().toLocaleDateString();
    }

    const productSelect = document.getElementById("productSelect");
    const productPriceInput = document.getElementById("productPrice");
    const invoiceItemsEl = document.getElementById('invoiceItems');
    const subtotalEl = document.getElementById('subtotalAmount');
    const vatEl = document.getElementById('vatAmount');
    const totalEl = document.getElementById('totalAmount');

    let items = [];

    productSelect.addEventListener("change", function () {
        const price = this.selectedOptions[0]?.dataset.price;
        productPriceInput.value = price ?? '';
    });

    document.getElementById("addProduct").addEventListener("click", (e) => {
        e.preventDefault();
        const name = productSelect.value;
        const qty = parseInt(document.getElementById('productQty').value);
        const price = parseFloat(productPriceInput.value);
        if (!name || qty <= 0 || price <= 0) return alert('Fill all fields');
        items.push({ name, qty, price });
        updateInvoiceTable();
        productSelect.value = '';
        document.getElementById('productQty').value = '';
        productPriceInput.value = '';
    });

    function updateInvoiceTable() {
    invoiceItemsEl.innerHTML = '';
    let subtotal = 0;

    items.forEach((item, index) => {
        const itemSubtotal = item.qty * item.price;
        subtotal += itemSubtotal;

        invoiceItemsEl.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>${item.qty}</td>
                <td>
                    <input type="number" class="form-control form-control-sm unit-price-input" 
                           data-index="${index}" value="${item.price.toFixed(2)}" />
                </td>
                <td class="item-subtotal">${itemSubtotal.toFixed(2)}</td>
            </tr>
        `;
    });

    const vat = subtotal * 0.16;
    const total = subtotal + vat;
    subtotalEl.textContent = subtotal.toFixed(2);
    vatEl.textContent = vat.toFixed(2);
    totalEl.textContent = total.toFixed(2);

    // Attach change event listeners to the editable price inputs
    document.querySelectorAll('.unit-price-input').forEach(input => {
        input.addEventListener('change', function () {
            const index = parseInt(this.dataset.index);
            const newPrice = parseFloat(this.value);

            if (!isNaN(newPrice) && newPrice >= 0) {
                items[index].price = newPrice;
                updateInvoiceTable(); // re-render and recalculate
            }
        });
    });
}


   document.getElementById('customerForm').addEventListener('submit', function (e) {
    e.preventDefault();

    document.getElementById('customerName').textContent = document.getElementById('inputCustomerName').value;
    document.getElementById('customerAddress').textContent = document.getElementById('inputCustomerAddress').value;
    document.getElementById('customerPhone').textContent = document.getElementById('inputCustomerPhone').value;
    document.getElementById('customerEmail').textContent = document.getElementById('inputCustomerEmail').value || '-';

    // Hide modal safely using Bootstrap instance
    const modalElement = document.getElementById('customerModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modalInstance.hide();
});




    document.getElementById('downloadPdf').addEventListener('click', () => {
        html2pdf().from(document.getElementById('invoice')).set({
            margin: 0.5,
            filename: 'Invoice_Pentagone.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        }).save();
    });
    document.getElementById('customerModal').addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = 'auto';
    document.querySelector('.modal-backdrop')?.remove();
});

});
function saveInvoiceToDB() {
    const invoiceData = {
        invoice_number: document.getElementById('invoiceNumber').textContent.trim(),
        customer_name: document.getElementById('customerName').textContent.trim(),
        customer_address: document.getElementById('customerAddress').textContent.trim(),
        customer_phone: document.getElementById('customerPhone').textContent.trim(),
        customer_email: document.getElementById('customerEmail').textContent.trim(),
        served_by: document.getElementById('servedBy').textContent.trim(),
        subtotal: parseFloat(document.getElementById('subtotalAmount').textContent),
        vat: parseFloat(document.getElementById('vatAmount').textContent),
        total: parseFloat(document.getElementById('totalAmount').textContent),
        items: items
    };

    fetch('{{ route("admin.invoices.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(invoiceData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Invoice saved successfully!');
        } else {
            alert('Error saving invoice');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving invoice');
    });
}

</script>
<script>
document.getElementById('submit-invoice').addEventListener('click', function () {
    const items = [];
    let subtotal = 0;

    const rows = document.querySelectorAll('#invoice-items tbody tr');
    rows.forEach(row => {
        const name = row.querySelector('.product-name').value;
        const qty = parseInt(row.querySelector('.product-qty').value);
        const price = parseFloat(row.querySelector('.product-price').value);
        const itemSubtotal = qty * price;
        subtotal += itemSubtotal;

        items.push({
            name: name,
            qty: qty,
            price: price
        });
    });

    const vat = subtotal * 0.16;
    const total = subtotal + vat;

    const data = {
        invoice_number: 'INV-' + Date.now(),
        customer_name: document.getElementById('customer_name').value,
        customer_address: document.getElementById('customer_address').value,
        customer_phone: document.getElementById('customer_phone').value,
        customer_email: document.getElementById('customer_email').value,
        served_by: document.getElementById('served_by').value,
        subtotal: subtotal.toFixed(2),
        vat: vat.toFixed(2),
        total: total.toFixed(2),
        items: items
    };

    fetch("{{ route('admin.invoices.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            alert('Invoice saved successfully! Invoice ID: ' + res.invoice_id);

            // Trigger download or print
            window.open(`/admin/invoice/${res.invoice_id}/print`, '_blank');
        } else {
            alert('Something went wrong while saving the invoice.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to submit invoice.');
    });
});
</script>


@endpush
