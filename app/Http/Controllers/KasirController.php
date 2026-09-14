<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function menu()
    {
        $menus = Menu::orderBy('nama_menu')->get();

        return view('backend.kasir.menu', compact('menus'));
    }

    public function stock()
    {
        $menus = Menu::orderBy('nama_menu')->get();

        return view('backend.kasir.stock', compact('menus'));
    }

    public function stockRestock(Request $request)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menu,id'],
            'amount'  => ['required', 'integer', 'min:1'],
        ]);

        $menu = Menu::findOrFail($data['menu_id']);
        $menu->increment('stok', $data['amount']);

        return redirect()->route('kasir.stock')
            ->with('success', 'Stok berhasil ditambahkan untuk ' . $menu->nama_menu);
    }

    public function report(Request $request)
    {
        $date = $request->query('date', today()->toDateString());

        $sales = Sale::whereDate('created_at', $date)->get();

        $reservations = Reservation::whereDate('tanggal_reservasi', $date)
            ->whereIn('status_reservasi', ['confirmed', 'completed'])
            ->get();

        $allReservations = Reservation::whereDate('tanggal_reservasi', $date)->get();

        $dailySalesItems = [];

        foreach ($sales as $sale) {
            $items = $sale->daftar_item;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'product' => $item['name'] ?? 'Unknown',
                        'customer' => 'Umum',
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => ucfirst($sale->metode_pembayaran ?? 'Cash'),
                    ];
                }
            }
        }

        foreach ($reservations as $res) {
            $items = $res->item_pesanan;
            if (is_array($items) && count($items) > 0) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'product' => $item['name'] ?? 'Unknown',
                        'customer' => $res->nama_pelanggan,
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => 'Reservasi (DP: ' . ucfirst($res->status_dp) . ')',
                    ];
                }
            } else {
                $dailySalesItems[] = [
                    'product' => 'Reservasi Meja',
                    'customer' => $res->nama_pelanggan,
                    'qty' => 1,
                    'price' => $res->total_harga,
                    'total' => $res->total_harga,
                    'payment' => 'Reservasi (DP: ' . ucfirst($res->status_dp) . ')',
                ];
            }
        }

        return view('backend.kasir.report', compact('dailySalesItems', 'allReservations', 'date'));
    }

    public function order()
    {
        $menus = Menu::orderBy('nama_menu')->get();

        return view('backend.kasir.order', compact('menus'));
    }

    public function orderCheckout(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'integer', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
            'total' => ['required', 'integer', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'amount_paid' => ['nullable', 'integer', 'min:0'],
            'change' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($data['items'] as $it) {
            $menu = Menu::where('nama_menu', $it['name'])->first();
            if ($menu) {
                $menu->decrement('stok', $it['qty']);
            }
        }

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'daftar_item' => $data['items'],
            'total' => $data['total'],
            'metode_pembayaran' => $data['payment_method'] ?? null,
            'nama_pelanggan' => $data['customer_name'] ?? null,
            'jumlah_bayar' => $data['amount_paid'] ?? 0,
            'kembalian' => $data['change'] ?? 0,
        ]);

        return response()->json(['ok' => true, 'sale_id' => $sale->id]);
    }

    public function midtransPay(Request $request)
    {
        $data = $request->validate([
            'sale_id' => ['required', 'integer', 'exists:penjualan,id'],
        ]);

        $sale = Sale::findOrFail($data['sale_id']);

        if (!$sale->kode) {
            $sale->update(['kode' => Sale::generateCode()]);
            $sale->refresh();
        }

        $this->setupMidtrans();

        $itemDetails = [];
        foreach ($sale->daftar_item as $item) {
            $itemDetails[] = [
                'id' => $item['name'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['qty'],
                'name' => $item['name'],
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $sale->kode,
                'gross_amount' => (int) $sale->total,
            ],
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => route('kasir.order'),
            ],
        ];

        try {
            $snapResponse = \Midtrans\Snap::createTransaction($params);
            return response()->json([
                'snap_token' => $snapResponse->token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat transaksi Midtrans.'], 500);
        }
    }

    public function confirmMidtrans(Request $request)
    {
        $data = $request->validate([
            'sale_id' => ['required', 'integer', 'exists:penjualan,id'],
        ]);

        $sale = Sale::findOrFail($data['sale_id']);

        if ($sale->status_pembayaran === 'settlement') {
            return response()->json(['ok' => true]);
        }

        try {
            $this->setupMidtrans();
            $status = \Midtrans\Transaction::status($sale->kode);
            $txStatus = $status->transaction_status ?? '';
            if (in_array($txStatus, ['settlement', 'capture'])) {
                $sale->update(['status_pembayaran' => 'settlement']);
                return response()->json(['ok' => true]);
            }
            return response()->json(['ok' => false, 'status' => $txStatus]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function reportExportPdf()
    {
        $date = request('date', today()->toDateString());

        $sales = Sale::whereDate('created_at', $date)->get();
        $reservations = Reservation::whereDate('tanggal_reservasi', $date)
            ->whereIn('status_reservasi', ['confirmed', 'completed'])
            ->get();
        $allReservations = Reservation::whereDate('tanggal_reservasi', $date)->get();

        $dailySalesItems = [];

        foreach ($sales as $sale) {
            $items = $sale->daftar_item;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'customer' => $sale->nama_pelanggan ?? 'Umum',
                        'product' => $item['name'] ?? 'Unknown',
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => ucfirst($sale->metode_pembayaran ?? 'Cash'),
                    ];
                }
            }
        }

        foreach ($reservations as $res) {
            $items = $res->item_pesanan;
            if (is_array($items) && count($items) > 0) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'customer' => $res->nama_pelanggan,
                        'product' => $item['name'] ?? 'Unknown',
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => 'Reservasi (' . ucfirst($res->status_dp) . ')',
                    ];
                }
            } else {
                $dailySalesItems[] = [
                    'customer' => $res->nama_pelanggan,
                    'product' => 'Reservasi Meja',
                    'qty' => 1,
                    'price' => $res->total_harga,
                    'total' => $res->total_harga,
                    'payment' => 'Reservasi (' . ucfirst($res->status_dp) . ')',
                ];
            }
        }

        $html = view('backend.kasir.pdf_report', compact('dailySalesItems', 'allReservations', 'date'))->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->stream('laporan-kasir-' . $date . '.pdf');
    }

    public function orderStatus(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->input('status'));
        }

        $sales = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('backend.kasir.order_status', compact('sales'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $request->validate([
            'payment_status' => ['required', 'string', 'in:pending,settlement,cancel,expire,deny'],
        ]);

        $sale->update([
            'status_pembayaran' => $request->input('payment_status'),
        ]);

        return back()->with('success', 'Status pesanan ' . ($sale->kode ?? '#' . $sale->id) . ' berhasil diperbarui.');
    }

    protected function setupMidtrans(): void
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$clientKey = config('midtrans.client_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }
}
