<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('name')->get();

        return view('backend.admin.menu', compact('menus'));
    }

    public function create()
    {
        return view('backend.admin.menu_create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:tersedia,habis,nonaktif'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeMenuImage($request);
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
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:tersedia,habis,nonaktif'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeMenuImage($request);
        } else {
            unset($data['image']);
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

