@extends('backend.kasir.layout')

@section('title', 'Status Pesanan')
@section('page_title', 'Status Pesanan')

@section('styles')
<style>
    .order-status-container {
        background: #faecd8;
        border-radius: 24px;
        padding: 24px;
        min-height: 80vh;
    }

    .filter-section {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-input-group {
        position: relative;
        flex: 1;
        min-width: 250px;
    }

    .search-input-group .icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        color: #555;
    }

    .search-bar {
        width: 100%;
        padding: 12px 20px 12px 48px;
        border-radius: 12px;
        border: 2px solid #354024;
        background: transparent;
        font-size: 14px;
        outline: none;
        color: #211b0f;
    }

    .filter-select {
        padding: 12px 16px;
        border-radius: 12px;
        border: 2px solid #354024;
        background: transparent;
        font-size: 14px;
        color: #211b0f;
        outline: none;
        min-width: 150px;
        font-family: inherit;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-search {
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        background: #354024;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-search:hover {
        opacity: 0.9;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 2px solid #354024;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 16px;
    }
    
    .custom-table thead {
        background: #efe0cd;
    }
    
    .custom-table th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 14px;
        color: #211b0f;
        border-bottom: 2px solid #354024;
    }
    
    .custom-table td {
        padding: 14px 16px;
        font-size: 13px;
        color: #211b0f;
        background: #efe0cd;
        border-bottom: 1px solid #354024;
        vertical-align: top;
    }
    
    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-success {
        background: rgba(53, 64, 36, 0.18);
        color: #354024;
    }
    .badge-danger {
        background: rgba(186, 26, 26, 0.18);
        color: #ba1a1a;
    }
    .badge-warning {
        background: rgba(200, 169, 81, 0.18);
        color: #8b6e1b;
    }
    .badge-secondary {
        background: rgba(100, 100, 100, 0.15);
        color: #555;
    }

    .order-items-list {
        margin: 0;
        padding-left: 16px;
        font-size: 12.5px;
        color: #45483f;
    }

    .order-items-list li {
        margin-bottom: 4px;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    /* Laravel simple bootstrap-like pagination styling */
    .pagination-wrapper nav {
        display: flex;
        gap: 8px;
    }
    .pagination-wrapper nav a, .pagination-wrapper nav span {
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #354024;
        background: #efe0cd;
        text-decoration: none;
        color: #211b0f;
        font-size: 13px;
        font-weight: 500;
    }
    .pagination-wrapper nav .active span {
        background: #354024;
        color: #fff;
    }
</style>
@endsection

@section('content')
    @if (session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:13px;font-weight:500;">
            {{ session('success') }}
        </div>
    @endif

    <div class="order-status-container">
        <!-- Filter and Search -->
        <form method="GET" action="{{ route('kasir.order_status') }}">
            <div class="filter-section">
                <div class="search-input-group">
                    <span class="icon">🔍</span>
                    <input 
                        type="text" 
                        name="search" 
                        class="search-bar" 
                        placeholder="Cari berdasarkan invoice / nama..." 
                        value="{{ request('search') }}"
                    >
                </div>

                <select name="status" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="settlement" {{ request('status') === 'settlement' ? 'selected' : '' }}>Settlement (Lunas)</option>
                    <option value="cancel" {{ request('status') === 'cancel' ? 'selected' : '' }}>Cancel</option>
                    <option value="expire" {{ request('status') === 'expire' ? 'selected' : '' }}>Expire</option>
                    <option value="deny" {{ request('status') === 'deny' ? 'selected' : '' }}>Deny</option>
                </select>

                <button type="submit" class="btn-search">Filter / Cari</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('kasir.order_status') }}" style="text-decoration:none; color:#8b6e1b; font-size:13px; font-weight:600; margin-left:8px;">Reset</a>
                @endif
            </div>
        </form>

        <!-- Orders Table -->
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Waktu / Tanggal</th>
                    <th>Kode / Inv</th>
                    <th>Nama Pelanggan</th>
                    <th>Item yang Dipesan</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th style="width: 230px;">Aksi Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>
                            <div>{{ $sale->created_at->format('d M Y') }}</div>
                            <div style="font-size:11px; color:#555; margin-top:2px;">{{ $sale->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td style="font-weight:600; color:#354024;">
                            {{ $sale->code ?? 'KASIR-' . $sale->id }}
                        </td>
                        <td>
                            {{ $sale->customer_name ?? 'Umum' }}
                        </td>
                        <td>
                            @if(is_array($sale->items) && count($sale->items) > 0)
                                <ul class="order-items-list">
                                    @foreach($sale->items as $item)
                                        <li>
                                            <strong>{{ $item['qty'] }}x</strong> {{ $item['name'] }}
                                            <span style="font-size:11px; color:#666;">
                                                (Rp{{ number_format($item['price'], 0, ',', '.') }})
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span style="color:#777; font-style:italic;">Tidak ada detail item</span>
                            @endif
                        </td>
                        <td style="font-weight:700;">
                            Rp{{ number_format($sale->total, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge" style="background:#efe0cd; border: 1px solid #354024; color:#354024; text-transform:none;">
                                {{ ucfirst($sale->payment_method ?? 'Cash') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = 'badge-secondary';
                                if ($sale->payment_status === 'settlement') {
                                    $statusClass = 'badge-success';
                                } elseif ($sale->payment_status === 'pending') {
                                    $statusClass = 'badge-warning';
                                } elseif (in_array($sale->payment_status, ['cancel', 'expire', 'deny'])) {
                                    $statusClass = 'badge-danger';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ $sale->payment_status }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('kasir.order_status.update', $sale->id) }}" method="POST" style="display:flex; gap:6px; align-items:center;">
                                @csrf
                                @method('PATCH')
                                <select name="payment_status" style="padding:8px 10px; border-radius:8px; border:1px solid #354024; background:#efe0cd; font-size:12px; outline:none; font-family:inherit; font-weight:500; cursor:pointer;">
                                    <option value="pending" {{ $sale->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="settlement" {{ $sale->payment_status === 'settlement' ? 'selected' : '' }}>Settlement (Lunas)</option>
                                    <option value="cancel" {{ $sale->payment_status === 'cancel' ? 'selected' : '' }}>Cancel</option>
                                    <option value="expire" {{ $sale->payment_status === 'expire' ? 'selected' : '' }}>Expire</option>
                                    <option value="deny" {{ $sale->payment_status === 'deny' ? 'selected' : '' }}>Deny</option>
                                </select>
                                <button type="submit" style="padding:8px 14px; border-radius:8px; background:#354024; color:#fff; border:none; cursor:pointer; font-size:11.5px; font-weight:700; transition: all 0.2s;">
                                    Update
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #354024; font-weight:500;">
                            Tidak ada pesanan ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $sales->links() }}
        </div>
    </div>
@endsection
