@extends('backend.admin.layout')

@section('title', 'Inventory')
@section('page_title', 'Inventory')

@section('content')
    @if (session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:13px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-card">
        <div style="border-radius:18px;overflow:hidden;background:#efe0cd;">
            <table>
                <thead>
                    <tr>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Stok Saat Ini</th>
                        <th style="width:260px;">Tambah Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td><span class="badge">{{ $item->category }}</span></td>
                            <td>{{ $item->stock }}</td>
                            <td>
                                <form action="{{ route('admin.inventory.restock') }}" method="POST" style="display:flex;gap:8px;align-items:center;">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $item->id }}">
                                    <input type="number" name="amount" min="1" value="10" required
                                        style="width:70px;padding:6px 10px;border-radius:8px;border:none;background:#FBEAC3;font-size:13px;">
                                    <button type="submit" class="btn-pill" style="padding:6px 14px;font-size:12px;">Restock</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #8B3A3A;">
                                Belum ada data menu
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
