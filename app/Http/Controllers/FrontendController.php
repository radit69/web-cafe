<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    public function home()
    {
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
    }

    public function menu(Request $request)
    {
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
    }

    public function menuDetail(Menu $menu)
    {
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
    }

    public function menuDetailRedirect()
    {
        $menu = Menu::where('status', '!=', 'nonaktif')->orderBy('name')->first();

        if (!$menu) {
            return redirect()->route('frontend.menu');
        }

        return redirect()->route('frontend.menu.detail', $menu);
    }

    public function cart()
    {
        $pajak = Setting::getValue('pajak', '10');
        $service = Setting::getValue('service', '5');

        return view('frontend.cart', compact('pajak', 'service'));
    }

    public function cartCheckout(Request $request)
    {
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

        $pajakRate = (float) Setting::getValue('pajak', '10');
        $serviceRate = (float) Setting::getValue('service', '5');
        $pajak = round($total * $pajakRate / 100);
        $serviceFee = round($total * $serviceRate / 100);
        $grandTotal = $total + $pajak + $serviceFee;

        $sale = Sale::create([
            'code' => Sale::generateCode(),
            'items' => $data['items'],
            'total' => $grandTotal,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
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
    }

    public function cartFinish(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionStatus = $request->query('transaction_status');

        $sale = $orderId ? Sale::where('code', $orderId)->first() : null;

        if ($sale && $sale->payment_status === 'pending' && in_array($transactionStatus, ['settlement', 'capture'])) {
            $sale->update(['payment_status' => 'settlement']);
        }

        if ($sale && $sale->payment_status === 'pending') {
            try {
                $this->setupMidtrans();
                $status = \Midtrans\Transaction::status($sale->code);
                $txStatus = $status->transaction_status ?? '';
                if (in_array($txStatus, ['settlement', 'capture'])) {
                    $sale->update(['payment_status' => 'settlement']);
                }
            } catch (\Exception $e) {
            }
        }

        $pajak = Setting::getValue('pajak', '10');
        $service = Setting::getValue('service', '5');

        return view('frontend.cart_finish', compact('sale', 'pajak', 'service'));
    }

    public function cartNotification(Request $request)
    {
        $this->setupMidtrans();

        $notification = new \Midtrans\Notification();
        $status = $notification->transaction_status;
        $orderId = $notification->order_id;

        $sale = Sale::where('code', $orderId)->first();
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
