@extends('backend.admin.layout')

@section('title', 'Manajemen Menu')
@section('page_title', 'Menu manajemen')

@section('content')
    <div class="top-actions">
        <a href="{{ route('admin.menus.create') }}" class="btn-pill">Tambah Menu</a>
    </div>

    <div class="content-card">
        <div style="margin-bottom:14px;">
            <input
                type="text"
                placeholder="Cari Menu..."
                style="width:100%;padding:10px 14px;border-radius:999px;border:none;font-size:13px;background:#efe0cd;"
            >
        </div>

        <div style="border-radius:18px;overflow:hidden;background:#efe0cd;">
            <table>
                <thead>
                    <tr>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stock</th>
                        <th style="width:110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menu)
                        <tr>
                            <td>{{ $menu->name }}</td>
                            <td><span class="badge">{{ $menu->category }}</span></td>
                            <td>Rp{{ number_format($menu->price, 0, ',', '.') }}</td>
                            <td>{{ $menu->stock }}</td>
                            <td>
                                <a href="{{ route('admin.menus.edit', $menu->id) }}" title="Edit">✏️</a>
                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;cursor:pointer;" title="Hapus">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


