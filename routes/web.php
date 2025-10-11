<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These routes
| are loaded by the RouteServiceProvider and all of them will be assigned to the
| "web" middleware group. Make something great!
|
*/

// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Page Routes
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/contact', [StaticPageController::class, 'contact'])->name('contact');
Route::post('/contact', [StaticPageController::class, 'contactSubmit'])->name('contact.submit');

// Authentication Routes (Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public Routes (Accessible to all, including guests)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('{product}', [ProductController::class, 'show'])->name('products.show');
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('{category}', [CategoryController::class, 'show'])->name('categories.show');
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/', [CartController::class, 'store'])->name('cart.store');
    Route::put('{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
});

Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'store'])->name('checkout.store');
});

// Authenticated Client Routes
Route::middleware('auth')->group(function () {
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('addresses.index');
        Route::get('create', [AddressController::class, 'create'])->name('addresses.create');
        Route::post('/', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    });
});

// Order and Payment Routes
Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

Route::prefix('payment')->group(function () {
    Route::post('/', [PaymentController::class, 'initialize'])->name('payment.initialize')->middleware('auth');
    Route::get('callback', [PaymentController::class, 'callback'])->name('payment.callback');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin|super_admin'])->group(function () {
    Route::get('dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::resource('banners', BannerController::class)->names('admin.banners');
    Route::resource('categories', AdminCategoryController::class)->names('admin.categories');
    Route::get('categories/search', [AdminCategoryController::class, 'search'])->name('admin.categories.search');
    Route::resource('coupons', CouponController::class)->names('admin.coupons')->only(['index', 'create', 'store']);
    Route::resource('inventories', InventoryController::class)->names('admin.inventories')->only(['index', 'create', 'store']);
    Route::resource('media', MediaController::class)->names('admin.media')->only(['index', 'create', 'store']);
    Route::resource('products', AdminProductController::class)->names('admin.products');
    Route::resource('purchases', PurchaseController::class)->names('admin.purchases')->only(['index', 'create', 'store']);
    Route::resource('orders', AdminOrderController::class)->names('admin.orders')->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('orders/{order}/drop', [AdminOrderController::class, 'drop'])->name('admin.orders.drop');
});

// Super-Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::resource('users', UserController::class)->names('admin.users');
});

// WebSocket Test Route
Route::get('test-websocket', function () {
    event(new \App\Events\TestEvent('Hello, Reverb!'));
    return view('test-websocket', ['message' => 'Event fired']);
})->name('test-websocket');

require __DIR__ . '/auth.php';