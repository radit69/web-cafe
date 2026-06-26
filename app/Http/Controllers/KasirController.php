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
        $menus = Menu::orderBy('name')->get();

        return view('backend.kasir.menu', compact('menus'));
    }

    public function stock()
    {
        $menus = Menu::orderBy('name')->get();

        return view('backend.kasir.stock', compact('menus'));
    }

    public function stockRestock(Request $request)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'amount'  => ['required', 'integer', 'min:1'],
        ]);

        $menu = Menu::findOrFail($data['menu_id']);
        $menu->increment('stock', $data['amount']);

        return redirect()->route('kasir.stock')
            ->with('success', 'Stok berhasil ditambahkan untuk ' . $menu->name);
    }

    public function report(Request $request)
    {
        $date = $request->query('date', today()->toDateString());

        $sales = Sale::whereDate('created_at', $date)->get();

        $reservations = Reservation::whereDate('reservation_date', $date)
            ->whereIn('status', ['confirmed', 'completed'])
            ->get();

        $allReservations = Reservation::whereDate('reservation_date', $date)->get();

        $dailySalesItems = [];

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
    }

    public function order()
    {
        $menus = Menu::orderBy('name')->get();

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
            $menu = Menu::where('name', $it['name'])->first();
            if ($menu) {
                $menu->decrement('stock', $it['qty']);
            }
        }

        $sale = Sale::create([
            'items' => $data['items'],
            'total' => $data['total'],
            'payment_method' => $data['payment_method'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'amount_paid' => $data['amount_paid'] ?? 0,
            'change' => $data['change'] ?? 0,
        ]);

        return response()->json(['ok' => true, 'sale_id' => $sale->id]);
    }

    public function midtransPay(Request $request)
    {
        $data = $request->validate([
            'sale_id' => ['required', 'integer', 'exists:sales,id'],
        ]);

        $sale = Sale::findOrFail($data['sale_id']);

        if (!$sale->code) {
            $sale->update(['code' => Sale::generateCode()]);
            $sale->refresh();
        }

        $this->setupMidtrans();

        $itemDetails = [];
        foreach ($sale->items as $item) {
            $itemDetails[] = [
                'id' => $item['name'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['qty'],
                'name' => $item['name'],
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $sale->code,
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
            'sale_id' => ['required', 'integer', 'exists:sales,id'],
        ]);

        $sale = Sale::findOrFail($data['sale_id']);

        if ($sale->payment_status === 'settlement') {
            return response()->json(['ok' => true]);
        }

        try {
            $this->setupMidtrans();
            $status = \Midtrans\Transaction::status($sale->code);
            $txStatus = $status->transaction_status ?? '';
            if (in_array($txStatus, ['settlement', 'capture'])) {
                $sale->update(['payment_status' => 'settlement']);
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
        $reservations = Reservation::whereDate('reservation_date', $date)
            ->whereIn('status', ['confirmed', 'completed'])
            ->get();
        $allReservations = Reservation::whereDate('reservation_date', $date)->get();

        $dailySalesItems = [];

        foreach ($sales as $sale) {
            $items = $sale->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'customer' => $sale->customer_name ?? 'Umum',
                        'product' => $item['name'] ?? 'Unknown',
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => ucfirst($sale->payment_method ?? 'Cash'),
                    ];
                }
            }
        }

        foreach ($reservations as $res) {
            $items = $res->order_items;
            if (is_array($items) && count($items) > 0) {
                foreach ($items as $item) {
                    $dailySalesItems[] = [
                        'customer' => $res->customer_name,
                        'product' => $item['name'] ?? 'Unknown',
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                        'payment' => 'Reservasi (' . ucfirst($res->dp_status) . ')',
                    ];
                }
            } else {
                $dailySalesItems[] = [
                    'customer' => $res->customer_name,
                    'product' => 'Reservasi Meja',
                    'qty' => 1,
                    'price' => $res->total_amount,
                    'total' => $res->total_amount,
                    'payment' => 'Reservasi (' . ucfirst($res->dp_status) . ')',
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
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
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
            'payment_status' => $request->input('payment_status'),
        ]);

        return back()->with('success', 'Status pesanan ' . ($sale->code ?? '#' . $sale->id) . ' berhasil diperbarui.');
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
