<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TenantSalesController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\StockReconciliationController;

// MASTER ADMIN ROUTES (Superadmin)
use App\Http\Controllers\Master\AdminController as MasterAdminController;
use App\Http\Controllers\Master\TenantController;

// ADMIN ROUTES (Tenant Admin)
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\TransactionController;


// USER ROUTES
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Models\Tenant;

// Redirect root to login
Route::get('/', fn () => redirect()->route('login'));


// AUTH ROUTES
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// COMMON DASHBOARD & PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// MASTER ADMIN (SUPERADMIN) ROUTES
Route::prefix('master')->middleware(['auth', 'superadmin'])->name('master.')->group(function () {
    Route::get('/dashboard', [MasterAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/tenants', [MasterAdminController::class, 'tenants'])->name('tenants');
    Route::get('/tenant/{id}/data', [MasterAdminController::class, 'tenantData'])->name('tenant.data');
});

// Master Tenant Routes
Route::prefix('master/tenants')->middleware(['auth', 'superadmin'])->name('master.tenant.')->group(function () {
    Route::get('/tenants', [TenantController::class, 'index'])->name('index');
    Route::get('/create', [TenantController::class, 'create'])->name('create');
    Route::post('/', [TenantController::class, 'store'])->name('store'); 
    Route::post('/{id}/toggle', [TenantController::class, 'toggle'])->name('toggle'); 
    Route::delete('/{id}', [TenantController::class, 'destroy'])->name('destroy'); 
    Route::get('/tenant/{id}/data', [TenantController::class, 'data'])->name('data');
     Route::post('/tenant/{id}/impersonate', [TenantController::class, 'impersonate'])->name('impersonate');
    Route::post('/stop-impersonate', [TenantController::class, 'stopImpersonate'])->name('stopImpersonate');
});


Route::prefix('admin')->middleware(['auth', 'tenant'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    

    // Products
    Route::resource('products', ProductController::class)->except(['show']);

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [AdminReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/today', [AdminReportController::class, 'today'])->name('reports.today');
    Route::get('/profits', [AdminReportController::class, 'profits'])->name('profits');


    // Users (manage tenant users)
    Route::resource('users', UserController::class)->except(['show']);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');

    // Invoice
    Route::get('/invoice', [InvoiceController::class, 'create'])->name('invoice');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/reports/monthly', [App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/admin/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

    Route::get('sales/create', [TenantSalesController::class, 'create'])->name('sales.create');
    Route::post('sales', [TenantSalesController::class, 'store'])->name('sales.store');
    Route::get('sales/receipt/{id}', [TenantSalesController::class, 'receipt'])->name('sales.receipt');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions/store-purchase', [TransactionController::class, 'storePurchase'])->name('transactions.storePurchase');
    Route::post('/admin/sale-multiple', [TransactionController::class, 'storeSaleMultiple'])->name('transactions.storeSaleMultiple');
    Route::post('/transactions/store-sale', [TransactionController::class, 'storeSale'])->name('transactions.storeSale');
    Route::get('/transactions/receipt/{sale}', [TransactionController::class, 'printReceipt'])->name('transactions.receipt');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases/store', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/reports/expiry', [App\Http\Controllers\Admin\ReportController::class, 'expiryReport'])->name('reports.expiry');
    

    Route::get('/stock-reconciliation', [StockReconciliationController::class, 'index'])->name('stock-reconciliation.index');
    Route::post('/stock-reconciliation', [StockReconciliationController::class, 'store'])->name('stock-reconciliation.store');
    Route::post('/stock-reconciliation/reconcile', [StockReconciliationController::class, 'reconcile'])->name('stock-reconciliation.reconcile');
    Route::get('/admin/reconciliation/export', [StockReconciliationController::class, 'exportPdf'])
    ->name('stock-reconciliation.export');
    Route::post('/admin/stock-reconciliation/update-system-stock', [StockReconciliationController::class, 'updateSystemStock'])->name('stock-reconciliation.updateSystemStock');

    Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');


});


// USER ROUTES
Route::middleware(['auth', 'tenant'])->name('user.')->group(function () {
    
    // Products
    Route::get('/products', [UserProductController::class, 'index'])->name('products.index');
    Route::post('/products/purchase', [UserProductController::class, 'purchase'])->name('products.purchase');
    Route::post('/products/sell', [UserProductController::class, 'sell'])->name('products.sell');
    Route::post('/products/sell-multiple', [UserProductController::class, 'sellMultiple'])->name('products.sell.multiple');

    // Reports
    Route::get('/reports', [UserReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/today', [UserReportController::class, 'today'])->name('today.report');
    

    // Receipt
    Route::get('/receipt/{saleId}', [UserProductController::class, 'printReceipt'])->name('receipt');

    Route::get('/expenses', [App\Http\Controllers\User\ExpensesController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [App\Http\Controllers\User\ExpensesController::class, 'store'])->name('expenses.store');
});




// Auth scaffolding routes (Breeze or Fortify)
require __DIR__.'/auth.php';
