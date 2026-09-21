<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\DigiflazzInquiryController;

// Home = list produk
Route::get('/', [ProductController::class, 'index'])->name('home');

// Produk
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// webhook midtrans
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');

Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('google.callback');

// Auth (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    
});

// Logout + pay (auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/products/{product}/pln-inquiry', [DigiflazzInquiryController::class, 'pln'])->name('products.plnInquiry');
    // POST simpan session
    Route::post('/products/{product}/preview', [ProductController::class, 'previewStore'])
        ->name('products.preview');

    // GET tampilkan preview
    Route::get('/products/{product}/preview', [ProductController::class, 'previewPage'])
        ->name('products.preview.get');
    
    Route::post('/products/{product}/pay', [OrderController::class, 'pay'])->name('products.pay');
    Route::get('/payment/finish/{order}', [OrderController::class, 'finish'])->name('payment.finish');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

});

// Buyer routes
Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('/buyer/dashboard', [BuyerController::class, 'dashboard'])->name('buyer.dashboard');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/retry', [AdminOrderController::class, 'retryFulfill'])->name('orders.retry');
        Route::post('/orders/{order}/mark-failed', [AdminOrderController::class, 'markFailed'])->name('orders.markFailed');
        Route::post('orders/{order}/check-digiflazz', [AdminOrderController::class, 'checkDigiflazz'])->name('orders.checkDigiflazz');


        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::post('/products/{product}/update', [AdminProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggleActive'])->name('products.toggle');
        Route::post('/products/sync-digiflazz', [AdminProductController::class, 'syncDigiflazz'])->name('products.sync');
        Route::post('/products/{product}/delete-image', [AdminProductController::class, 'deleteImage'])->name('products.deleteImage');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/role', [AdminUserController::class, 'setRole'])->name('users.role');
        Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggleActive'])->name('users.toggle');
    });
