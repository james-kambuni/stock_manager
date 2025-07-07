<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserDashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// User dashboard (Breeze layout)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});



Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');
});

// ---------------------------
// Admin routes (sidebar layout)
// ---------------------------
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class)->except(['show']);
     Route::get('/reports', [AdminReportController::class, 'generate'])->name('reports.generate');
     Route::get('/profits', [AdminReportController::class, 'profits'])->name('profits');
     Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/invoice', [InvoiceController::class, 'create'])->name('invoice');
});
Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
// Show product management interface for user


Route::middleware(['auth'])->group(function () {
    Route::get('/products', [UserProductController::class, 'index'])->name('products.index');
    Route::post('/products/purchase', [UserProductController::class, 'purchase'])->name('products.purchase');
    Route::post('/products/sell', [UserProductController::class, 'sell'])->name('products.sell');
    Route::get('/user/reports', [UserReportController::class, 'index'])->name('user.reports.index');
    Route::get('/user/today-report', [App\Http\Controllers\User\ReportController::class, 'today'])->name('user.today.report');

});

Route::middleware(['auth'])->group(function () {
    
});

Route::get('/admin/reports/today', [\App\Http\Controllers\Admin\ReportController::class, 'today'])->name('admin.reports.today');


require __DIR__.'/auth.php';

