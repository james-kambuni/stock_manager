@extends('layouts.users')

@section('title', 'Manage Products')

@section('content')
@include('partials.flash-messages')
<style>
    /* Improve base tab appearance */
    .nav-tabs .nav-link {
        color: #495057;
        transition: color 0.2s, background-color 0.2s;
    }

    /* Hover effect */
    .nav-tabs .nav-link:hover {
        color: #0d6efd; /* Bootstrap primary color */
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
        font-weight: 500;
        text-decoration: underline;
        text color: red;
    }

    /* Active tab styling for better contrast */
    .nav-tabs .nav-link.active {
        font-weight: 600;
        background-color: #ffffff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    /* Custom tab hover effect */
    .nav-tabs .nav-link {
        transition: background-color 0.3s ease, color 0.3s ease;
        border-radius: 0.375rem;
        font-weight: 500;
    }

    .nav-tabs .nav-link:hover {
        background-color: #f0f0f0;
        color: #0d6efd;
    }

    .nav-tabs .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .nav-tabs .nav-link i {
        margin-right: 5px;
    }
    .nav-tabs {
        justify-content: center; /* Center tabs even on small screens */
        flex-wrap: wrap;
    }

    .nav-tabs .nav-link {
        color: #343a40; /* default text color */
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }

    .nav-tabs .nav-link:hover {
        color: red !important;  /* Hover text turns red */
        background-color: #f8f9fa;
        text-decoration: underline;
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #0d6efd;
        border-color: #dee2e6 #dee2e6 #fff;
    }
</style>

<!-- Tabs Navigation -->
<!-- Tabs Navigation -->
<div class="d-flex justify-content-center overflow-auto mb-4">
    <ul class="nav nav-tabs flex-nowrap" id="stockTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase" type="button" role="tab">
                <i class="bi bi-box-arrow-in-down"></i> Purchases
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                <i class="bi bi-cash-coin"></i> Sales
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">
                <i class="bi bi-wallet2"></i> Expenses
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                <i class="bi bi-bar-chart-line"></i> Reports
            </button>
        </li>
    </ul>
</div>

<!-- Tabs Content -->
<div class="tab-content" id="stockTabsContent">
    <!-- Purchases Tab -->
    <div class="tab-pane fade show active" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
        @include('partials.purchase-card')
    </div>

    <!-- Sales Tab -->
    <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
        @include('partials.sale-card')
    </div>

    <!-- Expenses Tab -->
    <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#expenseModal">
                <i class="bi bi-plus-circle"></i> Record Expense
            </button>
        </div>
        @include('partials.expense-modal')
    </div>

    <!-- Reports Tab -->
    <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
        @include('partials.report-card')
    </div>
</div>

@endsection
