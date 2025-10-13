<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| - Guests can browse and checkout.
| - Registered users must verify their email before dashboard access.
| - Admins bypass email verification (auth + role check only).
|--------------------------------------------------------------------------
*/

// 🏠 Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// 📄 Static Pages
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/contact', [StaticPageController::class, 'contact'])->name('contact');
Route::post('/contact', [StaticPageController::class, 'contactSubmit'])->name('contact.submit');

// 🛍️ Public Shop
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('{product}', [ProductController::class, 'show'])->name('products.show');
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('{category}', [CategoryController::class, 'show'])->name('categories.show');
});

// 🛒 Cart (Guest + Auth)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/', [CartController::class, 'store'])->name('cart.store');
    Route::put('{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
});

// 💳 Checkout
Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'store'])->name('checkout.store');
});

// 📦 Order Creation (Guest or Auth)
Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

// 💰 Payment
Route::prefix('payment')->group(function () {
    Route::post('/', [PaymentController::class, 'initialize'])->name('payment.initialize')->middleware('auth');
    Route::get('callback', [PaymentController::class, 'callback'])->name('payment.callback');
});

// 👤 Registration (Verification Required)
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register')
    ->middleware('throttle:6,1');

// 📧 Email Verification Flow
Route::get('/verify-email', EmailVerificationPromptController::class)
    ->middleware('auth')
    ->name('verification.notice');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// 🧍‍♂️ Authenticated & Verified User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // 🖥️ Client Dashboard
    Route::get('/dashboard', fn () => view('dashboard'))
        ->middleware('role:client')
        ->name('dashboard');

    // 👤 Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 📦 Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    // 🏠 Addresses
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('addresses.index');
        Route::get('create', [AddressController::class, 'create'])->name('addresses.create');
        Route::post('/', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    });

    // 🔔 Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    });
});

// 🧑‍💼 Admin Routes (Bypass Email Verification)
Route::prefix('admin')
    ->middleware(['auth', 'role:admin|super-admin'])
    ->group(function () {
        // 🖥️ Admin Dashboard
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');

        // 🖼️ Banners
        Route::get('banners/search', [BannerController::class, 'search'])->name('admin.banner.search');
        Route::resource('banners', BannerController::class)->names('admin.banners');

        // 🗂️ Categories
        Route::get('categories/search', [AdminCategoryController::class, 'search'])->name('admin.categories.search');
        Route::resource('categories', AdminCategoryController::class)->names('admin.categories');

        // 🎟️ Coupons
        Route::get('coupons/search', [CouponController::class, 'search'])->name('admin.coupons.search');
        Route::resource('coupons', CouponController::class)->names('admin.coupons')->only(['index', 'create', 'store']);

        // 📊 Inventories
        Route::get('inventories/search', [InventoryController::class, 'search'])->name('admin.inventories.search');
        Route::resource('inventories', InventoryController::class)->names('admin.inventories')->only(['index', 'create', 'store']);

        // 🎨 Media
        Route::get('media/search', [MediaController::class, 'search'])->name('admin.media.search');
        Route::resource('media', MediaController::class)->names('admin.media')->only(['index', 'create', 'store']);

        // 🛒 Products
        Route::get('products/search', [AdminProductController::class, 'search'])->name('admin.products.search');
        Route::resource('products', AdminProductController::class)->names('admin.products');

        // 🛍️ Purchases
        Route::get('purchases/search', [PurchaseController::class, 'search'])->name('admin.purchases.search');
        Route::resource('purchases', PurchaseController::class)->names('admin.purchases')->only(['index', 'create', 'store']);

        // 🧾 Orders
        Route::get('orders/search', [AdminOrderController::class, 'search'])->name('admin.orders.search');
        Route::post('orders/{order}/drop', [AdminOrderController::class, 'drop'])->name('admin.orders.drop');
        Route::resource('orders', AdminOrderController::class)->names('admin.orders')->only(['index', 'show', 'edit', 'update', 'destroy']);

        // 🔔 Notifications
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
        Route::patch('/notifications/{notification}/read', [AdminNotificationController::class, 'markAsRead'])
            ->name('admin.notifications.read');

        // 👑 Profile
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::patch('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    });

// 👑 Super-Admin Only Routes
Route::prefix('admin')
    ->middleware(['auth', 'role:super-admin'])
    ->group(function () {
        // 👥 Users
        Route::get('users/search', [UserController::class, 'search'])->name('admin.users.search');
        Route::resource('users', UserController::class)->names('admin.users');
    });

// 🧪 WebSocket Test Route
Route::get('test-websocket', function () {
    event(new \App\Events\TestEvent('Hello, Reverb!'));
    return view('test-websocket', ['message' => 'Event fired']);
})->name('test-websocket');

// 🧭 Fallback (Redirect to Home)
Route::fallback(fn () => redirect()->route('home'));

// Auth scaffolding (Laravel Breeze / Fortify)
require __DIR__.'/auth.php';