<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 12px; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #354024; color: #fff; font-weight: 700; }
        tr:nth-child(even) td { background: #f5f0e8; }
        .total-row { font-weight: 700; font-size: 13px; }
        .summary { margin-bottom: 20px; }
        .summary-item { display: inline-block; margin-right: 30px; font-size: 13px; }
        .summary-item span { font-weight: 700; }
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <div class="subtitle">Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}</div>

    <div class="summary">
        <div class="summary-item">Total Pendapatan: <span>Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span></div>
        <div class="summary-item">Jumlah Transaksi: <span>{{ $totalOrders }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Item</th>
                <th>Total</th>
                <th>Pembayaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                @php $itemNames = collect($sale->items)->pluck('name')->implode(', '); @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->translatedFormat('d M Y H:i') }}</td>
                    <td>{{ $sale->code ?? '-' }}</td>
                    <td>{{ $sale->customer_name ?? 'Umum' }}</td>
                    <td>{{ $itemNames }}</td>
                    <td>Rp{{ number_format($sale->total, 0, ',', '.') }}</td>
                    <td>{{ $sale->payment_method ?? 'Midtrans' }}</td>
                    <td>{{ $sale->payment_status ?? 'settlement' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;">Tidak ada data penjualan</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ now()->translatedFormat('d M Y H:i') }}</div>
</body>
</html>