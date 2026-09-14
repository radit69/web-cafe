<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('nama_menu')->get();

        return view('backend.admin.menu', compact('menus'));
    }

    public function create()
    {
        return view('backend.admin.menu_create');
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:tersedia,habis,nonaktif'],
        ]);

        $data = [
            'nama_menu' => $requestData['name'],
            'kategori' => $requestData['category'],
            'deskripsi' => $requestData['description'] ?? null,
            'harga' => $requestData['price'],
            'stok' => $requestData['stock'],
            'status' => $requestData['status'],
        ];

        if ($request->hasFile('image')) {
            $data['gambar'] = $this->storeMenuImage($request);
        }

        Menu::create($data);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('backend.admin.menu_edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $requestData = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:tersedia,habis,nonaktif'],
        ]);

        $data = [
            'nama_menu' => $requestData['name'],
            'kategori' => $requestData['category'],
            'deskripsi' => $requestData['description'] ?? null,
            'harga' => $requestData['price'],
            'stok' => $requestData['stock'],
            'status' => $requestData['status'],
        ];

        if ($request->hasFile('image')) {
            $data['gambar'] = $this->storeMenuImage($request);
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    private function storeMenuImage(Request $request): string
    {
        $file = $request->file('image');
        $filename = uniqid('menu_', true) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('menu_images', $filename, 'public');

        return 'menu_images/' . $filename;
    }
}

