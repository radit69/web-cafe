@extends('backend.kasir.layout')

@section('title', 'Laporan Kasir')
@section('page_title', 'Laporan Akhir Kasir')

@section('styles')
<style>
    .report-section {
        margin-bottom: 24px;
        background: #faecd8;
        border-radius: 20px;
        padding: 24px;
    }

    .section-header {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #211b0f;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 2px solid #354024;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .custom-table thead {
        background: #efe0cd;
    }
    
    .custom-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 14px;
        color: #211b0f;
        border-bottom: 1px solid #354024;
    }
    
    .custom-table td {
        padding: 12px 16px;
        font-size: 14px;
        color: #211b0f;
        background: #efe0cd;
        border-bottom: 1px solid #354024;
    }
    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .badge-success {
        background: rgba(53, 64, 36, 0.18);
        color: #354024;
    }
    .badge-danger {
        background: rgba(186, 26, 26, 0.18);
        color: #ba1a1a;
    }
    .badge-primary {
        background: rgba(33, 150, 243, 0.18);
        color: #0d47a1;
    }
    .badge-warning {
        background: rgba(200, 169, 81, 0.18);
        color: #8b6e1b;
    }
</style>
@endsection

@section('content')
    <!-- Filter Tanggal -->
    <div style="margin-bottom: 24px;">
        <form method="GET" action="{{ route('kasir.report') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <div style="background:#faecd8; border: 2px solid #354024; border-radius: 12px; padding: 6px 12px; font-size: 13px; display: flex; gap: 6px; align-items: center;">
                <label for="date-filter" style="font-weight: 600; color: #211b0f; font-size: 12px; margin-right: 4px;">Tanggal:</label>
                <input 
                    type="date" 
                    id="date-filter"
                    name="date" 
                    value="{{ $date }}" 
                    style="border:none; background:transparent; font-size: 13px; outline:none; font-family:inherit; color:#211b0f; font-weight:500;"
                >
            </div>
            <button type="submit" class="filter-btn active" style="padding: 8px 16px; font-size: 13px; border-radius: 12px; border: 2px solid #354024; font-family: inherit; font-weight: 600; cursor: pointer;">
                Filter
            </button>
        </form>
    </div>

    <!-- Penjualan Harian -->
    <div class="report-section">
        <div class="section-header">Penjualan Harian ({{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }})</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Pelanggan</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th>Harga</th>
                    <th>Total Harga</th>
                    <th>Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dailySalesItems as $item)
                    <tr>
                        <td>{{ $item['product'] }}</td>
                        <td>{{ $item['customer'] }}</td>
                        <td style="text-align:center;">{{ $item['qty'] }}</td>
                        <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        <td>
                            <span class="badge" style="{{ str_contains(strtolower($item['payment']), 'reservasi') ? 'background:rgba(218, 232, 192, 0.4); color:#354024;' : 'background:rgba(33, 150, 243, 0.15); color:#0d47a1;' }}">
                                {{ $item['payment'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #354024; background: #efe0cd;">
                            Belum ada data penjualan harian untuk tanggal ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Laporan Reservasi -->
    <div class="report-section">
        <div class="section-header">Laporan Reservasi ({{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }})</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Kode Reservasi</th>
                    <th>Nama Pelanggan</th>
                    <th style="text-align:center;">Tamu</th>
                    <th>Waktu Reservasi</th>
                    <th>Status</th>
                    <th>Pembayaran DP</th>
                    <th>Total Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($allReservations as $res)
                    <tr>
                        <td style="font-weight: 600;">{{ $res->reservation_code }}</td>
                        <td>{{ $res->customer_name }}</td>
                        <td style="text-align:center;">{{ $res->guests }}</td>
                        <td>{{ substr($res->reservation_time, 0, 5) }} WIB</td>
                        <td>
                            @php
                                $statusBadgeClass = [
                                    'pending' => 'badge-warning',
                                    'confirmed' => 'badge-primary',
                                    'completed' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                ][$res->status] ?? 'badge-warning';
                            @endphp
                            <span class="badge {{ $statusBadgeClass }}">
                                {{ $res->status_label }}
                            </span>
                        </td>
                        <td>
                            @php
                                $dpBadgeClass = [
                                    'unpaid' => 'badge-danger',
                                    'paid' => 'badge-primary',
                                    'lunas' => 'badge-success',
                                ][$res->dp_status] ?? 'badge-danger';
                            @endphp
                            <span class="badge {{ $dpBadgeClass }}">
                                {{ $res->dp_status_label }}
                            </span>
                        </td>
                        <td style="font-weight: 600;">{{ $res->total_amount_formatted }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #354024; background: #efe0cd;">
                            Belum ada data reservasi untuk tanggal ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
