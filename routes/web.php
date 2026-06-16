<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Frontend (Public)
Route::get('/', [FrontendController::class, 'home'])->name('frontend.home');
Route::get('/menu', [FrontendController::class, 'menu'])->name('frontend.menu');
Route::get('/menu/detail', [FrontendController::class, 'menuDetailRedirect'])->name('frontend.detail');
Route::get('/menu/{menu}', [FrontendController::class, 'menuDetail'])->name('frontend.menu.detail');

// Reservasi
Route::get('/reservasi', [ReservationController::class, 'create'])->middleware('auth')->name('frontend.reservation');
Route::post('/reservasi', [ReservationController::class, 'store'])->middleware('auth')->name('frontend.reservation.store');
Route::post('/reservasi/notification', [ReservationController::class, 'notificationHandler'])->name('frontend.reservation.notification');
Route::post('/reservasi/{id}/batal', [ReservationController::class, 'cancel'])->middleware('auth')->name('frontend.reservation.cancel');
Route::post('/reservasi/{id}/bayar-dp', [ReservationController::class, 'payDp'])->middleware('auth')->name('frontend.reservation.pay_dp');
Route::post('/reservasi/{id}/confirm-payment', [ReservationController::class, 'confirmPayment'])->middleware('auth')->name('frontend.reservation.confirm_payment');
Route::post('/reservasi/{id}/bayar-sisa', [ReservationController::class, 'payRemaining'])->middleware('auth')->name('frontend.reservation.pay_remaining');
Route::get('/status', [ReservationController::class, 'status'])->middleware('auth')->name('frontend.status');

// Keranjang & Checkout
Route::get('/keranjang', [FrontendController::class, 'cart'])->name('frontend.cart');
Route::post('/keranjang/checkout', [FrontendController::class, 'cartCheckout'])->middleware('auth')->name('frontend.cart.checkout');
Route::get('/keranjang/selesai', [FrontendController::class, 'cartFinish'])->name('frontend.cart.finish');
Route::post('/keranjang/notification', [FrontendController::class, 'cartNotification'])->name('frontend.cart.notification');

// Auth Frontend
Route::get('/masuk', function () {
    return view('frontend.login');
})->name('login');
Route::post('/masuk', [AuthController::class, 'customerLogin'])->name('frontend.login.submit');
Route::get('/daftar', function () {
    return view('frontend.register');
})->name('frontend.register');
Route::post('/daftar', [AuthController::class, 'customerRegister'])->name('frontend.register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('frontend.logout');
Route::get('/profil', [AuthController::class, 'profileEdit'])->middleware('auth')->name('frontend.profile');
Route::post('/profil', [AuthController::class, 'profileUpdate'])->middleware('auth')->name('frontend.profile.update');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Backend Auth (public — login page & submit for admin/kasir)
Route::get('/backend', [AuthController::class, 'selectRole'])->name('select.role');
Route::get('/login/{role}', [AuthController::class, 'showLoginForm'])->name('login.role');
Route::post('/login/{role}', [AuthController::class, 'login'])->name('login.role.submit');

// Admin (wajib login & role admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::get('/customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');
    Route::patch('/customers/{customer}/toggle', [AdminCustomerController::class, 'toggle'])->name('customers.toggle');
    Route::resource('customers', AdminCustomerController::class)->except(['show']);
    Route::resource('menus', AdminMenuController::class)->except(['show']);
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/restock', [AdminInventoryController::class, 'restock'])->name('inventory.restock');

    Route::get('/sales', [AdminDashboardController::class, 'sales'])->name('sales');
    Route::get('/sales/export', [AdminDashboardController::class, 'salesExport'])->name('sales.export');

    Route::get('/reservations', [ReservationController::class, 'backendIndex'])->name('reservations');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::patch('/reservations/{id}/status', [ReservationController::class, 'updateStatusBackend'])->name('reservations.update_status');

    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminDashboardController::class, 'settingsSave'])->name('settings.save');
});

// Kasir (wajib login & role kasir)
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/menu', [KasirController::class, 'menu'])->name('menu');
    Route::get('/stock', [KasirController::class, 'stock'])->name('stock');
    Route::post('/stock/restock', [KasirController::class, 'stockRestock'])->name('stock.restock');
    Route::get('/report', [KasirController::class, 'report'])->name('report');
    Route::get('/reservations', [ReservationController::class, 'backendIndex'])->name('reservations');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::patch('/reservations/{id}/status', [ReservationController::class, 'updateStatusBackend'])->name('reservations.update_status');
    Route::get('/order', [KasirController::class, 'order'])->name('order');
    Route::post('/order/checkout', [KasirController::class, 'orderCheckout'])->name('order.checkout');
});
