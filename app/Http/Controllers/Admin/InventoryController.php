<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Menu::orderBy('nama_menu')->get();

        return view('backend.admin.inventory', compact('items'));
    }

    public function restock(Request $request)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menu,id'],
            'amount'  => ['required', 'integer', 'min:1'],
        ]);

        $menu = Menu::findOrFail($data['menu_id']);
        $menu->increment('stok', $data['amount']);

        return redirect()->route('admin.inventory')
            ->with('success', 'Stok berhasil ditambahkan untuk ' . $menu->nama_menu);
    }
}
