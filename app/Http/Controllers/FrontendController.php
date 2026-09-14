<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Sale;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $menus = Menu::where('status', '!=', 'nonaktif')
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get();

        $categories = Menu::where('status', '!=', 'nonaktif')
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $firstCategory = $categories->first();
        $featured = $menus->where('kategori', $firstCategory)->take(3);
        if ($featured->count() < 3) {
            $featured = $menus->take(3);
        }

        return view('frontend.index', compact('menus', 'categories', 'firstCategory', 'featured'));
    }

    public function menu(Request $request)
    {
        $category = $request->query('category');

        $menus = Menu::query()
            ->where('status', '!=', 'nonaktif')
            ->when($category, fn ($query) => $query->where('kategori', $category))
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get();

        $categories = Menu::query()
            ->where('status', '!=', 'nonaktif')
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('frontend.menu', compact('menus', 'categories', 'category'));
    }

    public function menuDetail(Menu $menu)
    {
        abort_if($menu->status === 'nonaktif', 404);

        $relatedMenus = Menu::where('status', '!=', 'nonaktif')
            ->where('id', '!=', $menu->id)
            ->when($menu->kategori, fn ($query) => $query->where('kategori', $menu->kategori))
            ->orderBy('nama_menu')
            ->take(3)
            ->get();

        if ($relatedMenus->isEmpty()) {
            $relatedMenus = Menu::where('status', '!=', 'nonaktif')
                ->where('id', '!=', $menu->id)
                ->orderBy('nama_menu')
                ->take(3)
                ->get();
        }

        $topMenus = Menu::where('status', '!=', 'nonaktif')
            ->orderByDesc('stok')
            ->orderBy('nama_menu')
            ->take(3)
            ->get();

        $categories = Menu::query()
            ->where('status', '!=', 'nonaktif')
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('frontend.detail', compact('menu', 'relatedMenus', 'topMenus', 'categories'));
    }

    public function menuDetailRedirect()
    {
        $menu = Menu::where('status', '!=', 'nonaktif')->orderBy('nama_menu')->first();

        if (!$menu) {
            return redirect()->route('frontend.menu');
        }

        return redirect()->route('frontend.menu.detail', $menu);
    }

    public function cart()
    {
        $pajak = '10';
        $service = '5';

        return view('frontend.cart', compact('pajak', 'service'));
    }

    public function cartCheckout(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:menu,id'],
            'items.*.name' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'integer', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $total = 0;
        foreach ($data['items'] as $item) {
            $menu = Menu::findOrFail($item['id']);
            if ($menu->stok < $item['qty']) {
                return back()->with('error', "Stok {$menu->nama_menu} tidak mencukupi. Sisa: {$menu->stok}.");
            }
            $menu->decrement('stok', $item['qty']);
            $total += $item['price'] * $item['qty'];
        }

        $pajakRate = (float) '10';
        $serviceRate = (float) '5';
        $pajak = round($total * $pajakRate / 100);
        $serviceFee = round($total * $serviceRate / 100);
        $grandTotal = $total + $pajak + $serviceFee;

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'kode' => Sale::generateCode(),
            'daftar_item' => $data['items'],
            'total' => $grandTotal,
            'metode_pembayaran' => 'midtrans',
            'status_pembayaran' => 'pending',
        ]);

        $this->setupMidtrans();

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
                'order_id' => $sale->kode,
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
    }

    public function cartFinish(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionStatus = $request->query('transaction_status');

        $sale = $orderId ? Sale::where('kode', $orderId)->first() : null;

        if ($sale && $sale->status_pembayaran === 'pending' && in_array($transactionStatus, ['settlement', 'capture'])) {
            $sale->update(['status_pembayaran' => 'settlement']);
        }

        if ($sale && $sale->status_pembayaran === 'pending') {
            try {
                $this->setupMidtrans();
                $status = \Midtrans\Transaction::status($sale->kode);
                $txStatus = $status->transaction_status ?? '';
                if (in_array($txStatus, ['settlement', 'capture'])) {
                    $sale->update(['status_pembayaran' => 'settlement']);
                }
            } catch (\Exception $e) {
            }
        }

        $pajak = '10';
        $service = '5';

        return view('frontend.cart_finish', compact('sale', 'pajak', 'service'));
    }

    public function cartNotification(Request $request)
    {
        $this->setupMidtrans();

        $notification = new \Midtrans\Notification();
        $status = $notification->transaction_status;
        $orderId = $notification->order_id;

        $sale = Sale::where('kode', $orderId)->first();
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
        $sale->update(['status_pembayaran' => $newStatus]);

        return response('OK', 200);
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
