<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TenantSalesController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\StockReconciliationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// MASTER ADMIN
use App\Http\Controllers\Master\AdminController as MasterAdminController;
use App\Http\Controllers\Master\TenantController;

// ADMIN (Tenant Admin)
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ServiceController;

// USER
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\Api\MpesaPaymentController;
use App\Http\Controllers\User\ServiceSaleController;

// ROOT
Route::get('/', fn () => redirect()->route('login'));

// AUTH
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



/*
|--------------------------------------------------------------------------
| MASTER ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('master')->middleware(['auth'])->name('master.')->group(function () {

    Route::get('/dashboard', [MasterAdminController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/tenants', [MasterAdminController::class, 'tenants'])
        ->name('tenants');

    // FIXED (no collision)
    Route::get('/tenant/{id}/view', [MasterAdminController::class, 'tenantData'])
        ->name('tenant.view');
});



Route::prefix('master/tenants')->middleware(['auth'])->name('master.tenant.')->group(function () {

    Route::get('/', [TenantController::class, 'index'])->name('index');
    Route::get('/create', [TenantController::class, 'create'])->name('create');
    Route::post('/', [TenantController::class, 'store'])->name('store');
    Route::post('/{id}/toggle', [TenantController::class, 'toggle'])->name('toggle');
    Route::delete('/{id}', [TenantController::class, 'destroy'])->name('destroy');

    // FIXED (unique name)
    Route::get('/{id}/data', [TenantController::class, 'data'])
        ->name('tenant.data');

    Route::post('/{id}/impersonate', [TenantController::class, 'impersonate'])->name('impersonate');
    Route::post('/stop-impersonate', [TenantController::class, 'stopImpersonate'])->name('stopImpersonate');
});



/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (TENANT ADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class)->except(['show']);

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [AdminReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/today', [AdminReportController::class, 'today'])->name('reports.today');
    Route::get('/profits', [AdminReportController::class, 'profits'])->name('profits');
    Route::get('/reports/monthly', [AdminReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/expiry', [AdminReportController::class, 'expiryReport'])->name('reports.expiry');

    Route::resource('users', UserController::class)->except(['show']);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');

    Route::get('/invoice', [InvoiceController::class, 'create'])->name('invoice');
    Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    Route::get('/sales/create', [TenantSalesController::class, 'create'])->name('sales.create');
    Route::post('/sales', [TenantSalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/receipt/{id}', [TenantSalesController::class, 'receipt'])->name('sales.receipt');

    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions/store-purchase', [TransactionController::class, 'storePurchase'])->name('transactions.storePurchase');
    Route::post('/transactions/store-sale', [TransactionController::class, 'storeSale'])->name('transactions.storeSale');
    Route::post('/transactions/sale-multiple', [TransactionController::class, 'storeSaleMultiple'])->name('transactions.storeSaleMultiple');
    Route::get('/transactions/receipt/{sale}', [TransactionController::class, 'printReceipt'])->name('transactions.receipt');

    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases/store', [PurchaseController::class, 'store'])->name('purchases.store');

    Route::get('/stock-reconciliation', [StockReconciliationController::class, 'index'])->name('stock.index');
    Route::post('/stock-reconciliation', [StockReconciliationController::class, 'store'])->name('stock.store');
    Route::post('/stock-reconciliation/reconcile', [StockReconciliationController::class, 'reconcile'])->name('stock.reconcile');
    Route::post('/stock-reconciliation/update', [StockReconciliationController::class, 'updateSystemStock'])->name('stock.updateSystemStock');
});



/*
|--------------------------------------------------------------------------
| USER ROUTES (TENANT USERS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->name('user.')->group(function () {

    Route::get('/products', [UserProductController::class, 'index'])->name('products.index');
    Route::post('/products/purchase', [UserProductController::class, 'purchase'])->name('products.purchase');
    Route::post('/products/sell', [UserProductController::class, 'sell'])->name('products.sell');
    Route::post('/products/sell-multiple', [UserProductController::class, 'sellMultiple'])->name('products.sell.multiple');

    Route::get('/reports', [UserReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/today', [UserReportController::class, 'today'])->name('reports.today');

    Route::get('/receipt/{saleId}', [UserProductController::class, 'printReceipt'])->name('receipt');

    Route::get('/expenses', [App\Http\Controllers\User\ExpensesController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [App\Http\Controllers\User\ExpensesController::class, 'store'])->name('expenses.store');
});



/*
|--------------------------------------------------------------------------
| PUBLIC (WRONG FIXED - NOW PROTECTED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::post('/services', [ServiceController::class, 'store'])->name('user.services.store');
    Route::post('/service-sales', [ServiceSaleController::class, 'store'])->name('user.service-sales.store');
});



/*
|--------------------------------------------------------------------------
| OTHER ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/mpesa/stk-push', [MpesaPaymentController::class, 'stkPush'])->name('mpesa.stk.push');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

require __DIR__.'/auth.php';