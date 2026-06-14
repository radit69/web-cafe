<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class ReservationController extends Controller
{
    public function create()
    {
        $menus = Menu::where('status', 'tersedia')
            ->where('stock', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $pajak = \App\Models\Setting::getValue('pajak', '10');
        $service = \App\Models\Setting::getValue('service', '5');

        return view('frontend.reservation', compact('menus', 'pajak', 'service'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'in:depok,cibubur'],
            'guests' => ['required', 'integer', 'min:1', 'max:10'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i', 'in:12:00,13:00,14:00,15:00,16:00,17:00,18:00,19:00,20:00,20:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'order_items' => ['nullable', 'array'],
            'order_items.*.id' => ['required', 'integer', 'exists:menus,id'],
            'order_items.*.name' => ['required', 'string'],
            'order_items.*.qty' => ['required', 'integer', 'min:1'],
            'order_items.*.price' => ['required', 'integer', 'min:0'],
        ]);

        $totalAmount = 0;
        $orderItems = $data['order_items'] ?? [];
        foreach ($orderItems as &$item) {
            $item['subtotal'] = $item['qty'] * $item['price'];
            $totalAmount += $item['subtotal'];
        }
        unset($item);

        $pajakRate = (float) \App\Models\Setting::getValue('pajak', '10');
        $serviceRate = (float) \App\Models\Setting::getValue('service', '5');
        $pajakAmount = round($totalAmount * $pajakRate / 100);
        $serviceAmount = round($totalAmount * $serviceRate / 100);
        $grandTotal = $totalAmount + $pajakAmount + $serviceAmount;

        $dpAmount = (int) round($grandTotal * 50 / 100);
        $remainingAmount = $grandTotal - $dpAmount;

        foreach ($orderItems as $item) {
            $menu = Menu::findOrFail($item['id']);
            if ($menu->stock < $item['qty']) {
                return redirect()->back()->withInput()->withErrors([
                    'stock' => "Stok {$menu->name} tidak mencukupi. Sisa: {$menu->stock}."
                ]);
            }
        }

        $reservation = DB::transaction(function () use ($data, $orderItems, $grandTotal, $dpAmount, $remainingAmount, $user) {
            $tableNumber = $this->assignTableNumber($data['date'], $data['time']);

            foreach ($orderItems as $item) {
                Menu::where('id', $item['id'])->where('stock', '>=', $item['qty'])
                    ->decrement('stock', $item['qty']);
            }

            return Reservation::create([
                'user_id' => $user?->role === 'pelanggan' ? $user->id : null,
                'reservation_code' => $this->makeReservationCode(),
                'customer_name' => $data['name'],
                'customer_email' => $data['email'] ?? $user?->email,
                'customer_phone' => $data['phone'] ?? $user?->phone,
                'guests' => $data['guests'],
                'reservation_date' => $data['date'],
                'reservation_time' => $data['time'],
                'notes' => $data['notes'] ?? null,
                'table_number' => $tableNumber,
                'location' => $data['location'] ?? 'depok',
                'order_items' => $orderItems,
                'total_amount' => $grandTotal,
                'dp_amount' => $dpAmount,
                'dp_status' => 'unpaid',
                'remaining_amount' => $remainingAmount,
                'status' => 'pending',
            ]);
        });

        if ($dpAmount > 0) {
            MidtransConfig::$serverKey = config('midtrans.server_key');
            MidtransConfig::$clientKey = config('midtrans.client_key');
            MidtransConfig::$isProduction = config('midtrans.is_production');
            MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
            MidtransConfig::$is3ds = config('midtrans.is_3ds');

            $orderId = $reservation->reservation_code . '-DP-' . time();

            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $dpAmount,
            ];

            $customerDetails = [
                'first_name' => $data['name'],
                'email' => $data['email'] ?? $user?->email,
                'phone' => $data['phone'] ?? $user?->phone,
            ];

            $itemDetails = [
                [
                    'id' => 'DP',
                    'price' => $dpAmount,
                    'quantity' => 1,
                    'name' => 'Uang Muka Reservasi (50%)',
                ],
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => route('frontend.status'),
                ],
            ];

            try {
                $snapResponse = Snap::createTransaction($params);
                session()->flash('snap_token', $snapResponse->token);
            } catch (\Exception $e) {
                return redirect()
                    ->route('frontend.status')
                    ->with('success', 'Reservasi berhasil dibuat, namun pembayaran DP gagal diproses. Silakan hubungi kami.')
                    ->with('reservation_id', $reservation->id);
            }
        }

        return redirect()
            ->route('frontend.status')
            ->with('success', 'Reservasi berhasil dibuat. Silakan lakukan pembayaran DP.')
            ->with('reservation_id', $reservation->id);
    }

    public function status()
    {
        $user = Auth::user();

        $query = Reservation::query()->latest('reservation_date')->latest('reservation_time');

        if ($user?->role === 'pelanggan') {
            $query->where('user_id', $user->id);
        } elseif (session('reservation_id')) {
            $query->where('id', session('reservation_id'));
        } else {
            $query->whereRaw('1 = 0');
        }

        $reservations = $query->get();

        $selectedId = request('id');
        if ($selectedId) {
            $activeReservation = $reservations->firstWhere('id', $selectedId) ??
                $reservations->first(function (Reservation $reservation) {
                    return ! in_array($reservation->status, ['completed', 'cancelled'], true);
                }) ?? $reservations->first();
        } else {
            $activeReservation = $reservations->first(function (Reservation $reservation) {
                return ! in_array($reservation->status, ['completed', 'cancelled'], true);
            }) ?? $reservations->first();
        }

        $pastReservations = $reservations
            ->reject(fn (Reservation $reservation) => $activeReservation && $reservation->id === $activeReservation->id)
            ->values();

        $willCharge = $activeReservation && \Carbon\Carbon::parse($activeReservation->reservation_date)->isBefore(now()->addDays(3)->startOfDay());

        $pelunasanHMin = (int) \App\Models\Setting::getValue('pelunasan_h_min', '1');
        $isHMin1 = $activeReservation && \Carbon\Carbon::parse($activeReservation->reservation_date)->subDays($pelunasanHMin)->startOfDay()->lte(now());

        $snapToken = session('snap_token');

        return view('frontend.status', compact(
            'reservations', 'activeReservation', 'pastReservations',
            'willCharge', 'isHMin1', 'snapToken'
        ));
    }

    public function payDp(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->dp_status !== 'unpaid') {
            return response()->json(['error' => 'DP sudah dibayar atau lunas.'], 400);
        }

        if ($reservation->dp_amount <= 0) {
            return response()->json(['error' => 'Tidak ada DP yang perlu dibayar.'], 400);
        }

        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$clientKey = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');

        $orderId = $reservation->reservation_code . '-DP-' . time();

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $reservation->dp_amount,
        ];

        $customerDetails = [
            'first_name' => $reservation->customer_name,
            'email' => $reservation->customer_email,
            'phone' => $reservation->customer_phone,
        ];

        $itemDetails = [
            [
                'id' => 'DP',
                'price' => $reservation->dp_amount,
                'quantity' => 1,
                'name' => 'Uang Muka Reservasi (50%)',
            ],
        ];

        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => route('frontend.status'),
            ],
        ];

        try {
            $snapResponse = Snap::createTransaction($params);
            return response()->json(['snap_token' => $snapResponse->token, 'order_id' => $orderId]);
        } catch (\Exception $e) {
            \Log::error('Midtrans DP payment error: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
                'order_id' => $orderId,
            ]);
            return response()->json(['error' => 'Gagal memproses pembayaran DP. Silakan coba lagi.'], 500);
        }
    }

    /**
     * Confirm payment after Midtrans snap callback (client-side).
     * This is needed because Midtrans webhooks cannot reach localhost.
     */
    public function confirmPayment(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $type = $request->input('type'); // 'dp' or 'remaining'
        $orderId = $request->input('order_id');

        if (!$orderId) {
            return response()->json(['error' => 'Order ID tidak ditemukan.'], 400);
        }

        // Verify the transaction status directly with Midtrans
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');

        try {
            $status = \Midtrans\Transaction::status($orderId);
            $transactionStatus = $status->transaction_status ?? '';

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                if ($type === 'dp') {
                    $reservation->update([
                        'dp_status' => 'paid',
                        'status' => 'confirmed',
                    ]);
                } elseif ($type === 'remaining') {
                    $reservation->update(['dp_status' => 'lunas']);
                }
                return response()->json(['success' => true, 'status' => $transactionStatus]);
            }

            // If pending, cancelled, or expired, do not update status in DB
            return response()->json(['success' => false, 'status' => $transactionStatus], 400);
        } catch (\Exception $e) {
            \Log::warning('Midtrans status check failed for order_id ' . $orderId . ': ' . $e->getMessage());
            // Do not update the status in the DB on failure or non-existent transaction
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function payRemaining(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->dp_status !== 'paid') {
            return response()->json(['error' => 'DP belum dibayar.'], 400);
        }

        if ($reservation->dp_status === 'lunas') {
            return response()->json(['error' => 'Pembayaran sudah lunas.'], 400);
        }

        $pelunasanHMin = (int) \App\Models\Setting::getValue('pelunasan_h_min', '1');
        $isHMin1 = \Carbon\Carbon::parse($reservation->reservation_date)->subDays($pelunasanHMin)->startOfDay()->lte(now());
        if (!$isHMin1) {
            return response()->json(['error' => 'Pembayaran sisa hanya bisa dilakukan H-' . $pelunasanHMin . ' atau hari-H reservasi.'], 400);
        }

        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$clientKey = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');

        $orderId = $reservation->reservation_code . '-LUNAS';

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $reservation->remaining_amount,
        ];

        $customerDetails = [
            'first_name' => $reservation->customer_name,
            'email' => $reservation->customer_email,
            'phone' => $reservation->customer_phone,
        ];

        $itemDetails = [
            [
                'id' => 'SISA',
                'price' => $reservation->remaining_amount,
                'quantity' => 1,
                'name' => 'Sisa Pembayaran Reservasi',
            ],
        ];

        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => route('frontend.status'),
            ],
        ];

        try {
            $snapResponse = Snap::createTransaction($params);
            return response()->json(['snap_token' => $snapResponse->token, 'order_id' => $orderId]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses pembayaran. Silakan coba lagi.'], 500);
        }
    }

    public function backendIndex()
    {
        $currentStatus = request('status', 'all');
        $currentDate = request('date');

        $reservations = Reservation::query()
            ->when($currentDate, fn ($query) => $query->whereDate('reservation_date', $currentDate))
            ->when($currentStatus !== 'all', fn ($query) => $query->where('status', $currentStatus))
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();

        $todayReservations = Reservation::whereDate('reservation_date', today())
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $stats = [
            'new_today' => Reservation::whereDate('created_at', today())->count(),
            'available_tables' => max(0, 15 - $todayReservations->count()),
            'total_tables' => 15,
            'occupancy' => min(100, (int) round(($todayReservations->sum('guests') / 72) * 100)),
        ];

        return view(request()->routeIs('admin.*') ? 'backend.admin.reservations' : 'backend.kasir.reservations', compact(
            'reservations',
            'stats',
            'currentStatus',
            'currentDate'
        ));
    }

    public function updateStatusBackend(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);

        $data = $request->validate([
            'status' => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'dp_status' => ['nullable', 'string', 'in:unpaid,paid,lunas'],
        ]);

        $updateData = array_filter($data);

        // If status is updated to completed, automatically mark dp_status as lunas if not already
        if (isset($updateData['status']) && $updateData['status'] === 'completed') {
            $updateData['dp_status'] = 'lunas';
        }

        // If dp_status is updated to lunas, update remaining_amount to 0
        if (isset($updateData['dp_status']) && $updateData['dp_status'] === 'lunas') {
            // Wait, does it also need to change the status of the reservation?
            // If they paid DP and sisa, status should at least be confirmed.
            if ($reservation->status === 'pending') {
                $updateData['status'] = 'confirmed';
            }
        }

        $reservation->update($updateData);

        return redirect()->back()->with('success', 'Status reservasi berhasil diperbarui.');
    }

    public function notificationHandler(Request $request)
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');

        $notification = new \Midtrans\Notification();

        $status = $notification->transaction_status;
        $orderId = $notification->order_id;

        // Detect transaction type: order_id formats are:
        // DP: RSV-xxx-DP or RSV-xxx-DP-{timestamp}
        // LUNAS: RSV-xxx-LUNAS or RSV-xxx-LUNAS-{timestamp}
        $isDp = (bool) preg_match('/-DP(-\d+)?$/', $orderId);
        $isLunas = (bool) preg_match('/-LUNAS(-\d+)?$/', $orderId);

        // Extract base reservation code by removing the suffix
        $baseOrderId = preg_replace('/-(DP|LUNAS)(-\d+)?$/', '', $orderId);

        $reservation = Reservation::where('reservation_code', $baseOrderId)->first();

        if (!$reservation) {
            return response('OK', 200);
        }

        if ($isDp) {
            if ($status === 'settlement' || $status === 'capture') {
                $reservation->update([
                    'dp_status' => 'paid',
                    'status' => 'confirmed',
                ]);
            } elseif ($status === 'deny' || $status === 'cancel' || $status === 'expire') {
                $reservation->update(['status' => 'cancelled']);
            }
        } elseif ($isLunas) {
            if ($status === 'settlement' || $status === 'capture') {
                $reservation->update(['dp_status' => 'lunas']);
            }
        } else {
            if ($status === 'settlement' || $status === 'capture') {
                $reservation->update(['status' => 'confirmed']);
            } elseif ($status === 'deny' || $status === 'cancel' || $status === 'expire') {
                $reservation->update(['status' => 'cancelled']);
            }
        }

        return response('OK', 200);
    }

    public function destroy(int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return redirect()->back()->with('success', 'Reservasi berhasil dihapus.');
    }

    public function cancel(int $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status === 'cancelled') {
            return redirect()->back()->with('info', 'Reservasi sudah dibatalkan sebelumnya.');
        }

        if (!in_array($reservation->status, ['pending', 'confirmed'], true)) {
            return redirect()->back()->with('error', 'Reservasi tidak dapat dibatalkan.');
        }

        $update = ['status' => 'cancelled'];

        $reservationDate = \Carbon\Carbon::parse($reservation->reservation_date);
        $threshold = now()->addDays(3)->startOfDay();

        if ($reservationDate->isBefore($threshold)) {
            $update['cancellation_charge'] = 50000;
        }

        $reservation->update($update);

        $message = $update['cancellation_charge'] ?? false
            ? 'Reservasi berhasil dibatalkan. Dikenakan charge Rp 50.000 karena pembatalan kurang dari 3 hari sebelum reservasi.'
            : 'Reservasi berhasil dibatalkan.';

        return redirect()->back()->with('success', $message);
    }

    private function makeReservationCode(): string
    {
        do {
            $code = 'RSV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
        } while (Reservation::where('reservation_code', $code)->exists());

        return $code;
    }

    private function assignTableNumber(string $date, string $time): ?int
    {
        $usedTables = Reservation::where('reservation_date', $date)
            ->where('reservation_time', $time)
            ->whereNotIn('status', ['cancelled'])
            ->whereNotNull('table_number')
            ->lockForUpdate()
            ->pluck('table_number')
            ->toArray();

        for ($i = 1; $i <= 15; $i++) {
            if (!in_array($i, $usedTables)) {
                return $i;
            }
        }

        return null;
    }
}
