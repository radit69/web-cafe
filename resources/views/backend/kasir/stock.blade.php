@extends('backend.kasir.layout')

@section('title', 'Stok Menu')
@section('page_title', 'Stok Menu')

@section('styles')
<style>
    .stock-container {
        background: #faecd8;
        border-radius: 24px;
        padding: 24px;
        min-height: 80vh;
        position: relative;
    }

    .category-row {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .category-label {
        font-size: 20px;
        font-weight: 700;
        color: #211b0f;
    }

    .cat-pill {
        padding: 8px 24px;
        border-radius: 20px;
        background: #efe0cd;
        border: 1px solid #354024;
        font-size: 14px;
        color: #211b0f;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cat-pill.active {
        background: #354024;
        color: #fff;
    }

    .search-row {
        margin-bottom: 24px;
    }
    .search-bar-full {
        width: 100%;
        padding: 14px 20px 14px 48px;
        border-radius: 12px;
        border: 1px solid #354024;
        background: transparent;
        font-size: 14px;
        outline: none;
    }

    .stock-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #efe0cd;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #354024;
    }

    .stock-table th, .stock-table td {
        padding: 14px 20px;
        text-align: left;
    }

    .stock-table thead {
        font-weight: 700;
        color: #211b0f;
        background: #efe0cd;
    }

    .stock-table tbody tr {
        border-top: 1px solid #354024;
    }
    .stock-table tbody tr:not(:last-child) {
        border-bottom: 1px solid #354024;
    }
    .stock-table tbody td {
        border-top: 1px solid #354024;
    }

    .action-buttons {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    
    .btn-action {
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
    }
    
    .btn-add {
        background: #E8D5B5;
        border: 1px solid #8B3A3A;
        color: #111;
    }
    
    .btn-export {
        background: #8B3A3A;
        color: #fff;
    }
</style>
@endsection

@section('content')
    @if (session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:13px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="stock-container">
        <div class="category-row">
            <span class="category-label">Kategori</span>
            <button class="cat-pill active" data-category="">Semua</button>
            @php
                $categories = $menus->pluck('category')->unique()->filter();
            @endphp
            @foreach ($categories as $cat)
                <button class="cat-pill" data-category="{{ $cat }}">{{ $cat }}</button>
            @endforeach
        </div>

        <div class="search-row" style="position:relative;">
            <span style="position:absolute; left:18px; top:50%; transform:translateY(-50%); font-size:18px;">🔍</span>
            <input type="text" class="search-bar-full" id="search-input" placeholder="Cari menu...">
        </div>

        <table class="stock-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align:center;">Stok</th>
                    <th>Status</th>
                    <th>Kategori</th>
                    <th style="width:220px;">Restock</th>
                </tr>
            </thead>
            <tbody id="stock-tbody">
                @forelse ($menus as $menu)
                    <tr class="stock-row" data-name="{{ strtolower($menu->name) }}" data-category="{{ $menu->category ?? '' }}">
                        <td>{{ $menu->name }}</td>
                        <td style="text-align:center;">{{ $menu->stock }}</td>
                        <td>
                            @if ($menu->stock > 0)
                                <span style="color:#2e7d32;">Tersedia</span>
                            @else
                                <span style="color:#c62828;">Habis</span>
                            @endif
                        </td>
                        <td>{{ $menu->category }}</td>
                        <td>
                            <form action="{{ route('kasir.stock.restock') }}" method="POST" style="display:flex;gap:8px;align-items:center;">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <input type="number" name="amount" min="1" value="10" required
                                    style="width:60px;padding:4px 8px;border-radius:6px;border:1px solid #354024;background:transparent;font-size:13px;">
                                <button type="submit" style="padding:6px 14px;border-radius:8px;border:1px solid #354024;background:#E8D5B5;cursor:pointer;font-size:12px;">Restock</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #354024;">
                            Belum ada data stok menu
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="action-buttons">
            <button class="btn-action btn-export" onclick="location.href='{{ route('kasir.menu') }}'">Lihat Menu</button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('search-input');
        var catPills = document.querySelectorAll('.cat-pill');
        var rows = document.querySelectorAll('.stock-row');

        function filterRows() {
            var query = searchInput.value.toLowerCase().trim();
            var activeCat = document.querySelector('.cat-pill.active').dataset.category;

            rows.forEach(function (row) {
                var name = row.dataset.name || '';
                var category = row.dataset.category || '';
                var matchName = name.includes(query);
                var matchCat = !activeCat || category === activeCat;
                row.style.display = (matchName && matchCat) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterRows);

        catPills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                catPills.forEach(function (p) { p.classList.remove('active'); });
                this.classList.add('active');
                filterRows();
            });
        });
    });
    </script>
@endsection
