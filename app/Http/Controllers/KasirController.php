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
    }
}
