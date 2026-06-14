<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Models\Sale;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Menu;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman Frontend
Route::get('/', function () {
    $menus = Menu::where('status', '!=', 'nonaktif')
        ->orderBy('category')
        ->orderBy('name')
        ->get();

    $categories = Menu::where('status', '!=', 'nonaktif')
        ->whereNotNull('category')
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    $firstCategory = $categories->first();
    $featured = $menus->where('category', $firstCategory)->take(3);
    if ($featured->count() < 3) {
        $featured = $menus->take(3);
    }

    return view('frontend.index', compact('menus', 'categories', 'firstCategory', 'featured'));
})->name('frontend.home');

Route::get('/menu', function (Request $request) {
    $category = $request->query('category');

    $menus = Menu::query()
        ->where('status', '!=', 'nonaktif')
        ->when($category, fn ($query) => $query->where('category', $category))
        ->orderBy('category')
        ->orderBy('name')
        ->get();

    $categories = Menu::query()
        ->where('status', '!=', 'nonaktif')
        ->whereNotNull('category')
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    return view('frontend.menu', compact('menus', 'categories', 'category'));
})->name('frontend.menu');

Route::get('/menu/detail', function () {
    $menu = Menu::where('status', '!=', 'nonaktif')->orderBy('name')->first();

    if (! $menu) {
        return redirect()->route('frontend.menu');
    }

    return redirect()->route('frontend.menu.detail', $menu);
})->name('frontend.detail');

Route::get('/menu/{menu}', function (Menu $menu) {
    abort_if($menu->status === 'nonaktif', 404);

    $relatedMenus = Menu::where('status', '!=', 'nonaktif')
        ->where('id', '!=', $menu->id)
        ->when($menu->category, fn ($query) => $query->where('category', $menu->category))
        ->orderBy('name')
        ->take(3)
        ->get();

    if ($relatedMenus->isEmpty()) {
        $relatedMenus = Menu::where('status', '!=', 'nonaktif')
            ->where('id', '!=', $menu->id)
            ->orderBy('name')
            ->take(3)
            ->get();
    }

    $topMenus = Menu::where('status', '!=', 'nonaktif')
        ->orderByDesc('stock')
        ->orderBy('name')
        ->take(3)
        ->get();

    $categories = Menu::query()
        ->where('status', '!=', 'nonaktif')
        ->whereNotNull('category')
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    return view('frontend.detail', compact('menu', 'relatedMenus', 'topMenus', 'categories'));
})->name('frontend.menu.detail');

Route::get('/reservasi', [ReservationController::class, 'create'])->middleware('auth')->name('frontend.reservation');
Route::post('/reservasi', [ReservationController::class, 'store'])->middleware('auth')->name('frontend.reservation.store');
Route::post('/reservasi/notification', [ReservationController::class, 'notificationHandler'])->name('frontend.reservation.notification');
Route::post('/reservasi/{id}/batal', [ReservationController::class, 'cancel'])->middleware('auth')->name('frontend.reservation.cancel');
Route::post('/reservasi/{id}/bayar-dp', [ReservationController::class, 'payDp'])->middleware('auth')->name('frontend.reservation.pay_dp');
Route::post('/reservasi/{id}/confirm-payment', [ReservationController::class, 'confirmPayment'])->middleware('auth')->name('frontend.reservation.confirm_payment');
Route::post('/reservasi/{id}/bayar-sisa', [ReservationController::class, 'payRemaining'])->middleware('auth')->name('frontend.reservation.pay_remaining');

Route::get('/status', [ReservationController::class, 'status'])->middleware('auth')->name('frontend.status');

Route::get('/keranjang', function () {
    $pajak = App\Models\Setting::getValue('pajak', '10');
    $service = App\Models\Setting::getValue('service', '5');
    return view('frontend.cart', compact('pajak', 'service'));
})->name('frontend.cart');

Route::post('/keranjang/checkout', function (Illuminate\Http\Request $request) {
    $data = $request->validate([
        'items' => ['required', 'array', 'min:1'],
        'items.*.id' => ['required', 'integer', 'exists:menus,id'],
        'items.*.name' => ['required', 'string'],
        'items.*.qty' => ['required', 'integer', 'min:1'],
        'items.*.price' => ['required', 'integer', 'min:0'],
    ]);

    $total = 0;
    foreach ($data['items'] as $item) {
        $menu = Menu::findOrFail($item['id']);
        if ($menu->stock < $item['qty']) {
            return back()->with('error', "Stok {$menu->name} tidak mencukupi. Sisa: {$menu->stock}.");
        }
        $menu->decrement('stock', $item['qty']);
        $total += $item['price'] * $item['qty'];
    }

    $pajakRate = (float) App\Models\Setting::getValue('pajak', '10');
    $serviceRate = (float) App\Models\Setting::getValue('service', '5');
    $pajak = round($total * $pajakRate / 100);
    $serviceFee = round($total * $serviceRate / 100);
    $grandTotal = $total + $pajak + $serviceFee;

    $sale = Sale::create([
        'code' => \App\Models\Sale::generateCode(),
        'items' => $data['items'],
        'total' => $grandTotal,
        'payment_method' => 'midtrans',
        'payment_status' => 'pending',
    ]);

    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$clientKey = config('midtrans.client_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');
    \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
    \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

    $itemDetails = [];
    foreach ($data['items'] as $item) {
        $itemDetails[] = [
            'id' => (string) $item['id'],
            'price' => $item['price'],
            'quantity' => $item['qty'],
            'name' => $item['name'],
        ];
    }
    if ($pajak > 0) {
        $itemDetails[] = ['id' => 'PAJAK', 'price' => $pajak, 'quantity' => 1, 'name' => 'Pajak (' . $pajakRate . '%)'];
    }
    if ($serviceFee > 0) {
        $itemDetails[] = ['id' => 'SERVICE', 'price' => $serviceFee, 'quantity' => 1, 'name' => 'Biaya Service (' . $serviceRate . '%)'];
    }

    $params = [
        'transaction_details' => [
            'order_id' => $sale->code,
            'gross_amount' => $grandTotal,
        ],
        'item_details' => $itemDetails,
        'callbacks' => [
            'finish' => route('frontend.cart.finish'),
        ],
    ];

    try {
        $snapResponse = \Midtrans\Snap::createTransaction($params);
        return redirect()->away($snapResponse->redirect_url);
    } catch (\Exception $e) {
        return redirect()->route('frontend.cart')
            ->with('error', 'Pembayaran gagal diproses. Silakan coba lagi.');
    }
})->middleware('auth')->name('frontend.cart.checkout');

Route::get('/keranjang/selesai', function (\Illuminate\Http\Request $request) {
    $orderId = $request->query('order_id');
    $transactionStatus = $request->query('transaction_status');

    $sale = $orderId ? \App\Models\Sale::where('code', $orderId)->first() : null;

    if ($sale && $sale->payment_status === 'pending' && $transactionStatus === 'settlement') {
        $sale->update(['payment_status' => 'settlement']);
    } elseif ($sale && $sale->payment_status === 'pending' && $transactionStatus === 'capture') {
        $sale->update(['payment_status' => 'settlement']);
    }

    if ($sale && $sale->payment_status === 'pending') {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            $status = \Midtrans\Transaction::status($sale->code);
            if (($status->transaction_status ?? '') === 'settlement' || ($status->transaction_status ?? '') === 'capture') {
                $sale->update(['payment_status' => 'settlement']);
            }
        } catch (\Exception $e) {}
    }

    $pajak = \App\Models\Setting::getValue('pajak', '10');
    $service = \App\Models\Setting::getValue('service', '5');

    return view('frontend.cart_finish', compact('sale', 'pajak', 'service'));
})->name('frontend.cart.finish');

Route::post('/keranjang/notification', function (\Illuminate\Http\Request $request) {
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');

    $notification = new \Midtrans\Notification();
    $status = $notification->transaction_status;
    $orderId = $notification->order_id;

    $sale = \App\Models\Sale::where('code', $orderId)->first();
    if (!$sale) {
        return response('OK', 200);
    }

    $statusMap = [
        'settlement' => 'settlement',
        'capture' => 'settlement',
        'pending' => 'pending',
        'deny' => 'deny',
        'cancel' => 'cancel',
        'expire' => 'expire',
    ];

    $newStatus = $statusMap[$status] ?? 'pending';
    $sale->update(['payment_status' => $newStatus]);

    return response('OK', 200);
})->name('frontend.cart.notification');

Route::get('/masuk', function () {
    return view('frontend.login');
})->name('login');
Route::post('/masuk', [AuthController::class, 'customerLogin'])->name('frontend.login.submit');

Route::get('/daftar', function () {
    return view('frontend.register');
})->name('frontend.register');
Route::post('/daftar', [AuthController::class, 'customerRegister'])->name('frontend.register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('frontend.logout');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Halaman pilih user (backend login)
Route::get('/backend', [AuthController::class, 'selectRole'])->name('select.role');

// Halaman form login untuk tiap role
Route::get('/login/{role}', [AuthController::class, 'showLoginForm'])->name('login.role');
Route::post('/login/{role}', [AuthController::class, 'login'])->name('login.role.submit');

// Halaman-halaman admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $today = now()->toDateString();
        $dailyTransactionsCount = Sale::whereDate('created_at', $today)->count();
        $dailyRevenue = Sale::whereDate('created_at', $today)->sum('total');
        $userCount = User::count();
        $menuCount = Menu::where('status', '!=', 'nonaktif')->count();
        $reservationCount = Reservation::count();

        // Weekly chart (7 hari)
        $weekLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $weekTotals = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $weekTotals[] = Sale::whereDate('created_at', $date)->sum('total');
        }

        // Monthly chart (12 bulan)
        $monthLabels = [];
        $monthTotals = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M');
            $monthTotals[] = Sale::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total');
        }

        return view('backend.admin.dashboard', compact(
            'dailyTransactionsCount', 'dailyRevenue', 'userCount', 'menuCount',
            'weekLabels', 'weekTotals', 'monthLabels', 'monthTotals',
            'reservationCount'
        ));
    })->name('dashboard');

    // CRUD user admin
    Route::resource('users', AdminUserController::class)->except(['show']);

    // Manajemen customer frontend
    Route::get('/customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');
    Route::patch('/customers/{customer}/toggle', [AdminCustomerController::class, 'toggle'])->name('customers.toggle');
    Route::resource('customers', AdminCustomerController::class)->except(['show']);

    // CRUD menu + manajemen stok
    Route::resource('menus', AdminMenuController::class)->except(['show']);

    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/restock', [AdminInventoryController::class, 'restock'])->name('inventory.restock');

    Route::get('/sales', function () {
        return view('backend.admin.sales');
    })->name('sales');

    Route::get('/reservations', [ReservationController::class, 'backendIndex'])->name('reservations');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::patch('/reservations/{id}/status', [ReservationController::class, 'updateStatusBackend'])->name('reservations.update_status');

    Route::get('/sales/export', function () {
        // Export laporan penjualan (dummy)
        return "Export laporan penjualan (contoh).";
    })->name('sales.export');

    Route::get('/settings', function () {
        $pajak = App\Models\Setting::getValue('pajak', '10');
        $service = App\Models\Setting::getValue('service', '5');
        $jamBuka = App\Models\Setting::getValue('jam_buka', '08:00');
        $jamTutup = App\Models\Setting::getValue('jam_tutup', '21:00');
        $pelunasanHMin = App\Models\Setting::getValue('pelunasan_h_min', '1');

        return view('backend.admin.settings', compact('pajak', 'service', 'jamBuka', 'jamTutup', 'pelunasanHMin'));
    })->name('settings');

    Route::post('/settings', function (Request $request) {
        $data = $request->validate([
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i'],
            'pajak' => ['required', 'numeric', 'min:0', 'max:100'],
            'service' => ['required', 'numeric', 'min:0', 'max:100'],
            'pelunasan_h_min' => ['required', 'integer', 'min:0', 'max:30'],
        ]);

        foreach ($data as $key => $value) {
            App\Models\Setting::setValue($key, $value);
        }

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    })->name('settings.save');
});


// Halaman-halaman Kasir
Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/menu', function () {
        $menus = Menu::orderBy('name')->get();
        return view('backend.kasir.menu', compact('menus'));
    })->name('menu');

    Route::get('/stock', function () {
        $menus = Menu::orderBy('name')->get();
        return view('backend.kasir.stock', compact('menus'));
    })->name('stock');

    Route::post('/stock/restock', function (Request $request) {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'amount'  => ['required', 'integer', 'min:1'],
        ]);

        $menu = Menu::findOrFail($data['menu_id']);
        $menu->increment('stock', $data['amount']);

        return redirect()->route('kasir.stock')
            ->with('success', 'Stok berhasil ditambahkan untuk ' . $menu->name);
    })->name('stock.restock');

    Route::get('/report', function (Request $request) {
        $date = $request->query('date', today()->toDateString());

        // Ambil data penjualan harian (Sale)
        $sales = Sale::whereDate('created_at', $date)->get();

        // Ambil data reservasi hari ini yang confirmed atau completed
        $reservations = Reservation::whereDate('reservation_date', $date)
            ->whereIn('status', ['confirmed', 'completed'])
            ->get();

        // Ambil semua reservasi hari ini untuk Laporan Reservasi
        $allReservations = Reservation::whereDate('reservation_date', $date)->get();

        // Compile list barang yang terjual dari Sale & Reservation
        $dailySalesItems = [];

        // 1. Loop through normal Sales
        foreach ($sales as $sale) {
            $items = $sale->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'product' => $item['name'] ?? 'Unknown',
                        'customer' => 'Umum',
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => ucfirst($sale->payment_method ?? 'Cash'),
                    ];
                }
            }
        }

        // 2. Loop through Reservations
        foreach ($reservations as $res) {
            $items = $res->order_items;
            if (is_array($items) && count($items) > 0) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'product' => $item['name'] ?? 'Unknown',
                        'customer' => $res->customer_name,
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => 'Reservasi (DP: ' . ucfirst($res->dp_status) . ')',
                    ];
                }
            } else {
                // If there are no order items, but it has a total amount (reservation fee / DP)
                $dailySalesItems[] = [
                    'product' => 'Reservasi Meja',
                    'customer' => $res->customer_name,
                    'qty' => 1,
                    'price' => $res->total_amount,
                    'total' => $res->total_amount,
                    'payment' => 'Reservasi (DP: ' . ucfirst($res->dp_status) . ')',
                ];
            }
        }

        return view('backend.kasir.report', compact('dailySalesItems', 'allReservations', 'date'));
    })->name('report');

    Route::get('/reservations', [ReservationController::class, 'backendIndex'])->name('reservations');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::patch('/reservations/{id}/status', [ReservationController::class, 'updateStatusBackend'])->name('reservations.update_status');

    Route::get('/order', function () {
        $menus = Menu::orderBy('name')->get();
        return view('backend.kasir.order', compact('menus'));
    })->name('order');

    Route::post('/order/checkout', function (Request $request) {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'integer', 'min:0'],
            'total' => ['required', 'integer', 'min:0'],
            'payment_method' => ['nullable', 'string'],
        ]);

        foreach ($data['items'] as $it) {
            $menu = Menu::where('name', $it['name'])->first();
            if ($menu) {
                $menu->decrement('stock', $it['qty']);
            }
        }

        $sale = Sale::create([
            'items' => $data['items'],
            'total' => $data['total'],
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        return response()->json(['ok' => true, 'sale_id' => $sale->id]);
    })->name('order.checkout');
});
