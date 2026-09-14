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
            ->where('stok', '>', 0)
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get();

        $pajak = '10';
        $service = '5';

        return view('frontend.reservation', compact('menus', 'pajak', 'service'));
    }

    public function availableTables(Request $request)
    {
        $date = $request->input('date');
        $time = $request->input('time');

        if (!$date || !$time) {
            return response()->json(['tables' => []]);
        }

        $usedTables = Reservation::where('tanggal_reservasi', $date)
            ->where('jam_reservasi', $time)
            ->whereNotIn('status_reservasi', ['cancelled'])
            ->whereNotNull('nomor_meja')
            ->pluck('nomor_meja')
            ->toArray();

        $tables = [];
        for ($i = 1; $i <= 15; $i++) {
            $tables[] = [
                'number' => $i,
                'available' => !in_array($i, $usedTables),
                'label' => "Meja $i",
            ];
        }

        return response()->json(['tables' => $tables]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'in:depok,cibubur'],
            'guests' => ['required', 'integer', 'min:1', 'max:100'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i', 'in:12:00,13:00,14:00,15:00,16:00,17:00,18:00,19:00,20:00,20:30'],
            'table_number' => ['required', 'integer', 'min:1', 'max:15'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'order_items' => ['nullable', 'array'],
            'order_items.*.id' => ['required', 'integer', 'exists:menu,id'],
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

        $pajakRate = (float) '10';
        $serviceRate = (float) '5';
        $pajakAmount = round($totalAmount * $pajakRate / 100);
        $serviceAmount = round($totalAmount * $serviceRate / 100);
        $grandTotal = $totalAmount + $pajakAmount + $serviceAmount;

        $dpAmount = (int) round($grandTotal * 50 / 100);
        $remainingAmount = $grandTotal - $dpAmount;

        foreach ($orderItems as $item) {
            $menu = Menu::findOrFail($item['id']);
            if ($menu->stok < $item['qty']) {
                return redirect()->back()->withInput()->withErrors([
                    'stock' => "Stok {$menu->nama_menu} tidak mencukupi. Sisa: {$menu->stok}."
                ]);
            }
        }

        try {
            $reservation = DB::transaction(function () use ($data, $orderItems, $grandTotal, $dpAmount, $remainingAmount, $user) {
                $isTaken = Reservation::where('tanggal_reservasi', $data['date'])
                    ->where('jam_reservasi', $data['time'])
                    ->whereNotIn('status_reservasi', ['cancelled'])
                    ->where('nomor_meja', $data['table_number'])
                    ->lockForUpdate()
                    ->exists();

                if ($isTaken) {
                    throw new \Exception('Maaf, meja yang dipilih sudah dipesan. Silakan pilih meja lain.');
                }

                foreach ($orderItems as $item) {
                    Menu::where('id', $item['id'])->where('stok', '>=', $item['qty'])
                        ->decrement('stok', $item['qty']);
                }

                return Reservation::create([
                    'user_id' => $user?->role === 'pelanggan' ? $user->id : null,
                    'kode_reservasi' => $this->makeReservationCode(),
                    'nama_pelanggan' => $data['name'],
                    'email_pelanggan' => $data['email'] ?? $user?->email,
                    'telepon_pelanggan' => $data['phone'] ?? $user?->phone,
                    'jumlah_orang' => $data['guests'],
                    'tanggal_reservasi' => $data['date'],
                    'jam_reservasi' => $data['time'],
                    'catatan' => $data['notes'] ?? null,
                    'nomor_meja' => $data['table_number'],
                    'lokasi' => $data['location'] ?? 'depok',
                    'item_pesanan' => $orderItems,
                    'total_harga' => $grandTotal,
                    'jumlah_dp' => $dpAmount,
                    'status_dp' => 'unpaid',
                    'sisa_pembayaran' => $remainingAmount,
                    'status_reservasi' => 'pending',
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['table_number' => $e->getMessage()]);
        }

        if ($dpAmount > 0) {
            $this->setupMidtrans();

            $orderId = $reservation->kode_reservasi . '-DP-' . time();

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

        $query = Reservation::query()->latest('tanggal_reservasi')->latest('jam_reservasi');

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
                    return ! in_array($reservation->status_reservasi, ['completed', 'cancelled'], true);
                }) ?? $reservations->first();
        } else {
            $activeReservation = $reservations->first(function (Reservation $reservation) {
                return ! in_array($reservation->status_reservasi, ['completed', 'cancelled'], true);
            }) ?? $reservations->first();
        }

        $pastReservations = $reservations
            ->reject(fn (Reservation $reservation) => $activeReservation && $reservation->id === $activeReservation->id)
            ->values();

        $willCharge = $activeReservation && \Carbon\Carbon::parse($activeReservation->tanggal_reservasi)->isBefore(now()->addDays(3)->startOfDay());

        $pelunasanHMin = 1;
        $isHMin1 = $activeReservation && \Carbon\Carbon::parse($activeReservation->tanggal_reservasi)->subDays($pelunasanHMin)->startOfDay()->lte(now());

        $snapToken = session('snap_token');

        return view('frontend.status', compact(
            'reservations', 'activeReservation', 'pastReservations',
            'willCharge', 'isHMin1', 'snapToken'
        ));
    }

    public function payDp(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status_dp !== 'unpaid') {
            return response()->json(['error' => 'DP sudah dibayar atau lunas.'], 400);
        }

        if ($reservation->jumlah_dp <= 0) {
            return response()->json(['error' => 'Tidak ada DP yang perlu dibayar.'], 400);
        }

        $this->setupMidtrans();

        $orderId = $reservation->kode_reservasi . '-DP-' . time();

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $reservation->jumlah_dp,
        ];

        $customerDetails = [
            'first_name' => $reservation->nama_pelanggan,
            'email' => $reservation->email_pelanggan,
            'phone' => $reservation->telepon_pelanggan,
        ];

        $itemDetails = [
            [
                'id' => 'DP',
                'price' => $reservation->jumlah_dp,
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

        $this->setupMidtrans();

        try {
            $status = \Midtrans\Transaction::status($orderId);
            $transactionStatus = $status->transaction_status ?? '';

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                if ($type === 'dp') {
                    $reservation->update([
                        'status_dp' => 'paid',
                        'status_reservasi' => 'confirmed',
                    ]);
                } elseif ($type === 'remaining') {
                    $reservation->update(['status_dp' => 'lunas']);
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

        if ($reservation->status_dp !== 'paid') {
            return response()->json(['error' => 'DP belum dibayar.'], 400);
        }

        if ($reservation->status_dp === 'lunas') {
            return response()->json(['error' => 'Pembayaran sudah lunas.'], 400);
        }

        $pelunasanHMin = 1;
        $isHMin1 = \Carbon\Carbon::parse($reservation->tanggal_reservasi)->subDays($pelunasanHMin)->startOfDay()->lte(now());
        if (!$isHMin1) {
            return response()->json(['error' => 'Pembayaran sisa hanya bisa dilakukan H-' . $pelunasanHMin . ' atau hari-H reservasi.'], 400);
        }

        $this->setupMidtrans();

        $orderId = $reservation->kode_reservasi . '-LUNAS';

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $reservation->sisa_pembayaran,
        ];

        $customerDetails = [
            'first_name' => $reservation->nama_pelanggan,
            'email' => $reservation->email_pelanggan,
            'phone' => $reservation->telepon_pelanggan,
        ];

        $itemDetails = [
            [
                'id' => 'SISA',
                'price' => $reservation->sisa_pembayaran,
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
            ->when($currentDate, fn ($query) => $query->whereDate('tanggal_reservasi', $currentDate))
            ->when($currentStatus !== 'all', fn ($query) => $query->where('status_reservasi', $currentStatus))
            ->orderBy('tanggal_reservasi')
            ->orderBy('jam_reservasi')
            ->get();

        $todayReservations = Reservation::whereDate('tanggal_reservasi', today())
            ->whereNotIn('status_reservasi', ['cancelled'])
            ->get();

        $stats = [
            'new_today' => Reservation::whereDate('created_at', today())->count(),
            'available_tables' => max(0, 15 - $todayReservations->count()),
            'total_tables' => 15,
            'occupancy' => min(100, (int) round(($todayReservations->sum('jumlah_orang') / 72) * 100)),
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

        $updateData = [];
        if (isset($data['status'])) {
            $updateData['status_reservasi'] = $data['status'];
        }
        if (isset($data['dp_status'])) {
            $updateData['status_dp'] = $data['dp_status'];
        }

        // If status is updated to completed, automatically mark status_dp as lunas if not already
        if (isset($updateData['status_reservasi']) && $updateData['status_reservasi'] === 'completed') {
            $updateData['status_dp'] = 'lunas';
        }

        // If status_dp is updated to lunas, update remaining_amount to 0
        if (isset($updateData['status_dp']) && $updateData['status_dp'] === 'lunas') {
            if ($reservation->status_reservasi === 'pending') {
                $updateData['status_reservasi'] = 'confirmed';
            }
        }

        $reservation->update($updateData);

        return redirect()->back()->with('success', 'Status reservasi berhasil diperbarui.');
    }

    public function notificationHandler(Request $request)
    {
        $this->setupMidtrans();

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

        $reservation = Reservation::where('kode_reservasi', $baseOrderId)->first();

        if (!$reservation) {
            return response('OK', 200);
        }

        if ($isDp) {
            if ($status === 'settlement' || $status === 'capture') {
                $reservation->update([
                    'status_dp' => 'paid',
                    'status_reservasi' => 'confirmed',
                ]);
            } elseif ($status === 'deny' || $status === 'cancel' || $status === 'expire') {
                $reservation->update(['status_reservasi' => 'cancelled']);
            }
        } elseif ($isLunas) {
            if ($status === 'settlement' || $status === 'capture') {
                $reservation->update(['status_dp' => 'lunas']);
            }
        } else {
            if ($status === 'settlement' || $status === 'capture') {
                $reservation->update(['status_reservasi' => 'confirmed']);
            } elseif ($status === 'deny' || $status === 'cancel' || $status === 'expire') {
                $reservation->update(['status_reservasi' => 'cancelled']);
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

        if ($reservation->status_reservasi === 'cancelled') {
            return redirect()->back()->with('info', 'Reservasi sudah dibatalkan sebelumnya.');
        }

        if (!in_array($reservation->status_reservasi, ['pending', 'confirmed'], true)) {
            return redirect()->back()->with('error', 'Reservasi tidak dapat dibatalkan.');
        }

        $update = ['status_reservasi' => 'cancelled'];

        $reservationDate = \Carbon\Carbon::parse($reservation->tanggal_reservasi);
        $threshold = now()->addDays(3)->startOfDay();

        if ($reservationDate->isBefore($threshold)) {
            $update['biaya_pembatalan'] = 50000;
        }

        $reservation->update($update);

        $message = $update['biaya_pembatalan'] ?? false
            ? 'Reservasi berhasil dibatalkan. Dikenakan charge Rp 50.000 karena pembatalan kurang dari 3 hari sebelum reservasi.'
            : 'Reservasi berhasil dibatalkan.';

        return redirect()->back()->with('success', $message);
    }

    private function makeReservationCode(): string
    {
        do {
            $code = 'RSV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
        } while (Reservation::where('kode_reservasi', $code)->exists());

        return $code;
    }

    protected function setupMidtrans(): void
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$clientKey = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }
}
