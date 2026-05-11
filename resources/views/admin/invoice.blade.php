@extends('layouts.admin')

@section('title', 'Invoice Generator')

@push('styles')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* ================= PRINT ================= */

@media print {

    body * {
        visibility: hidden;
    }

    #invoice,
    #invoice * {
        visibility: visible;
    }

    #invoice {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        padding: 5px;
        margin: 0;
        border: none;
        font-size: 9px;
    }

    .no-print {
        display: none !important;
    }
}

/* ================= MAIN ================= */

#invoice {
    background: #fff;
    border: 1px solid #ddd;
    padding: 8px;
    font-size: 10px;
    line-height: 1;
}

/* REMOVE ALL EXTRA SPACING */

#invoice p,
#invoice h1,
#invoice h2,
#invoice h3,
#invoice h4,
#invoice h5,
#invoice h6,
#invoice div,
#invoice td,
#invoice th {
    margin: 0 !important;
    padding-top: 1px !important;
    padding-bottom: 1px !important;
    line-height: 1 !important;
}

/* HEADER */

#invoice-header {
    margin-bottom: 4px !important;
}

#invoice-header img {
    max-width: 70px;
    margin-bottom: 2px;
}

#invoice-header h3 {
    font-size: 12px;
    font-weight: bold;
}

#invoice-header p {
    font-size: 9px;
}

/* TABLE */

.table {
    margin-bottom: 4px !important;
}

.table th,
.table td {
    font-size: 9px;
    padding: 2px 4px !important;
    vertical-align: middle;
    white-space: nowrap;
}

/* CUSTOMER SECTION */

.customer-section p,
.served-section p {
    margin: 0 !important;
    line-height: 1 !important;
    font-size: 9px;
}

/* PAYMENT DETAILS */

#bank-details,
#terms {
    margin-top: 4px !important;
    font-size: 8px;
    line-height: 1 !important;
}

#bank-details h6,
#terms h6 {
    font-size: 9px;
    margin-bottom: 1px !important;
}

#bank-details p,
#terms p {
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1 !important;
    font-size: 8px;
}

/* MODAL FIX */

.modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1055 !important;
}

.modal-content {
    border-radius: 12px;
}

.form-control-sm {
    padding: 1px 3px !important;
    font-size: 9px;
    min-height: 22px;
}

</style>

@endpush

@section('content')

<div class="container my-3">

    <h4 class="text-center text-primary fw-bold mb-3">
        Invoice Generator
    </h4>

    <!-- ACTION BUTTONS -->

    <div class="text-end mb-3 no-print">

        <button type="button"
                class="btn btn-secondary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#customerModal">
            Customer Details
        </button>

        <button type="button"
                class="btn btn-primary btn-sm"
                onclick="saveInvoiceToDB()">
            Save Invoice
        </button>

        <button type="button"
                class="btn btn-outline-primary btn-sm"
                onclick="window.print()">
            Print
        </button>

        <button type="button"
                class="btn btn-outline-danger btn-sm"
                id="downloadPdf">
            PDF
        </button>

    </div>

    <!-- PRODUCT ENTRY -->

    <div class="card p-3 mb-3 no-print shadow-sm">

        <div class="row g-2 align-items-end">

            <div class="col-md-5">

                <label class="form-label">
                    Product
                </label>

                <select class="form-select"
                        id="productSelect">

                    <option value="">
                        Select product
                    </option>

                    @foreach($products as $product)

                        <option value="{{ $product->name }}"
                                data-price="{{ $product->selling_price }}">

                            {{ $product->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="col-md-3">

                <label class="form-label">
                    Qty
                </label>

                <input type="number"
                       id="productQty"
                       class="form-control">

            </div>

            <div class="col-md-3">

                <label class="form-label">
                    Unit Price
                </label>

                <input type="number"
                       id="productPrice"
                       class="form-control"
                       readonly>

            </div>

            <div class="col-md-1">

                <button type="button"
                        id="addProduct"
                        class="btn btn-success w-100">

                    Add

                </button>

            </div>

        </div>

    </div>

    <!-- INVOICE -->

    <div id="invoice">

        @php
            $tenant = auth()->user()->tenant;
            $invoiceNumber = 'INV-' . strtoupper(Str::random(6));
        @endphp

        <!-- HEADER -->

        <div id="invoice-header" class="text-center">

            @if($tenant && $tenant->logo)

                <img src="{{ asset('storage/' . $tenant->logo) }}"
                     alt="Logo">

            @endif

            <h3>Jtech IT Consultants</h3>
            <p>Mwingi</p>
            <p>Pin: A011970484C</p>
            <p>0700369827</p>

        </div>

        <!-- CUSTOMER + STAFF -->

        <div class="row mb-1">

            <div class="col-6 customer-section">

                <h6>Customer Details</h6>

                <p>
                    <strong>Name:</strong>
                    <span id="customerName">-</span>
                </p>

                <p>
                    <strong>Address:</strong>
                    <span id="customerAddress">-</span>
                </p>

                <p>
                    <strong>Phone:</strong>
                    <span id="customerPhone">-</span>
                </p>

                <p>
                    <strong>Email:</strong>
                    <span id="customerEmail">-</span>
                </p>

            </div>

            <div class="col-6 text-end served-section">

                <h6>Invoice Details</h6>

                <p>
                    <strong>Served By:</strong>
                    {{ $user->name }}
                </p>

                <p>
                    <strong>Date:</strong>
                    {{ now()->format('d M Y') }}
                </p>

                <p>
                    <strong>Invoice #:</strong>
                    <span id="invoiceNumber">
                        {{ $invoiceNumber }}
                    </span>
                </p>

            </div>

        </div>

        <!-- ITEMS TABLE -->

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

                    <th colspan="3" class="text-end">
                        Subtotal
                    </th>

                    <th id="subtotalAmount">
                        0.00
                    </th>

                </tr>

                <tr>

                    <th colspan="3" class="text-end">
                        VAT (16%)
                    </th>

                    <th id="vatAmount">
                        0.00
                    </th>

                </tr>

                <tr>

                    <th colspan="3" class="text-end">
                        Total
                    </th>

                    <th id="totalAmount">
                        0.00
                    </th>

                </tr>

            </tfoot>

        </table>

        <!-- PAYMENT DETAILS -->

        <div id="bank-details">

            <h6>Payment Details</h6>

            <p>Paybill (KCB)</p>
            <p>Business No: 522533</p>
            <p>Acc No: 4243142012171673</p>
            <p>Name: Jtech IT Consultants</p>

        </div>

        <!-- TERMS -->

        <div id="terms">

            <h6>Terms & Conditions</h6>

            <p>
                Payment due within 30 days.
            </p>

            <p>
                Late payments may attract charges.
            </p>

        </div>

    </div>

</div>

<!-- CUSTOMER MODAL -->

<div class="modal fade"
     id="customerModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form id="customerForm">

                <div class="modal-header bg-primary text-white">

                    <h5 class="modal-title">
                        Customer Details
                    </h5>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-2">

                        <label>Name</label>

                        <input type="text"
                               id="inputCustomerName"
                               class="form-control">

                    </div>

                    <div class="mb-2">

                        <label>Address</label>

                        <input type="text"
                               id="inputCustomerAddress"
                               class="form-control">

                    </div>

                    <div class="mb-2">

                        <label>Phone</label>

                        <input type="text"
                               id="inputCustomerPhone"
                               class="form-control">

                    </div>

                    <div class="mb-2">

                        <label>Email</label>

                        <input type="email"
                               id="inputCustomerEmail"
                               class="form-control">

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Close

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        Save

                    </button>

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

let items = [];

document.addEventListener('DOMContentLoaded', function () {

    const productSelect = document.getElementById('productSelect');
    const productPrice = document.getElementById('productPrice');

    productSelect.addEventListener('change', function () {

        productPrice.value =
            this.selectedOptions[0]?.dataset.price || '';

    });

    document.getElementById('addProduct')
        .addEventListener('click', function () {

        const name = productSelect.value;

        const qty =
            parseInt(document.getElementById('productQty').value);

        const price =
            parseFloat(productPrice.value);

        if (!name || qty <= 0 || price <= 0) {

            alert('Fill all fields');

            return;
        }

        items.push({
            name,
            qty,
            price
        });

        renderInvoice();

        productSelect.value = '';
        document.getElementById('productQty').value = '';
        productPrice.value = '';
    });

    function renderInvoice() {

        const tbody =
            document.getElementById('invoiceItems');

        tbody.innerHTML = '';

        let subtotal = 0;

        items.forEach((item, index) => {

            const rowTotal =
                item.qty * item.price;

            subtotal += rowTotal;

            tbody.innerHTML += `
                <tr>
                    <td>${item.name}</td>

                    <td>${item.qty}</td>

                    <td>
                        <input type="number"
                               value="${item.price}"
                               data-index="${index}"
                               class="form-control form-control-sm unit-price">
                    </td>

                    <td>${rowTotal.toFixed(2)}</td>
                </tr>
            `;
        });

        const vat = subtotal * 0.16;
        const total = subtotal + vat;

        document.getElementById('subtotalAmount')
            .textContent = subtotal.toFixed(2);

        document.getElementById('vatAmount')
            .textContent = vat.toFixed(2);

        document.getElementById('totalAmount')
            .textContent = total.toFixed(2);

        document.querySelectorAll('.unit-price')
            .forEach(input => {

            input.addEventListener('change', function () {

                const index =
                    parseInt(this.dataset.index);

                items[index].price =
                    parseFloat(this.value);

                renderInvoice();
            });
        });
    }

    // CUSTOMER FORM

    document.getElementById('customerForm')
        .addEventListener('submit', function (e) {

        e.preventDefault();

        document.getElementById('customerName').textContent =
            document.getElementById('inputCustomerName').value;

        document.getElementById('customerAddress').textContent =
            document.getElementById('inputCustomerAddress').value;

        document.getElementById('customerPhone').textContent =
            document.getElementById('inputCustomerPhone').value;

        document.getElementById('customerEmail').textContent =
            document.getElementById('inputCustomerEmail').value || '-';

        const modalEl =
            document.getElementById('customerModal');

        const modal =
            bootstrap.Modal.getOrCreateInstance(modalEl);

        modal.hide();

        document.querySelectorAll('.modal-backdrop')
            .forEach(el => el.remove());

        document.body.classList.remove('modal-open');

        document.body.style = '';
    });

    // PDF

    document.getElementById('downloadPdf')
        .addEventListener('click', function () {

        const invoiceNum =
            document.getElementById('invoiceNumber')
                .textContent.trim();

        html2pdf()
            .from(document.getElementById('invoice'))
            .set({
                margin: 0.2,
                filename: `Invoice_${invoiceNum}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait'
                }
            })
            .save();
    });

});

// SAVE TO DB

function saveInvoiceToDB() {

    const invoiceData = {

        invoice_number:
            document.getElementById('invoiceNumber')
                .textContent.trim(),

        customer_name:
            document.getElementById('customerName')
                .textContent.trim(),

        customer_address:
            document.getElementById('customerAddress')
                .textContent.trim(),

        customer_phone:
            document.getElementById('customerPhone')
                .textContent.trim(),

        customer_email:
            document.getElementById('customerEmail')
                .textContent.trim(),

        subtotal:
            parseFloat(
                document.getElementById('subtotalAmount')
                    .textContent
            ),

        vat:
            parseFloat(
                document.getElementById('vatAmount')
                    .textContent
            ),

        total:
            parseFloat(
                document.getElementById('totalAmount')
                    .textContent
            ),

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

            alert('Invoice saved successfully');

        } else {

            alert('Error saving invoice');
        }
    })

    .catch(error => {

        console.error(error);

        alert('Error saving invoice');
    });
}

</script>

@endpush