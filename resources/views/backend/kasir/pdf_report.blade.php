<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kasir</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 11px; color: #666; margin-bottom: 20px; }
        h2 { font-size: 13px; margin-top: 24px; margin-bottom: 8px; color: #354024; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background: #354024; color: #fff; font-weight: 700; }
        tr:nth-child(even) td { background: #f5f0e8; }
        .footer { text-align: center; font-size: 9px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Laporan Akhir Kasir</h1>
    <div class="subtitle">Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</div>

    <h2>Penjualan Harian</h2>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Pelanggan</th>
                <th style="text-align:center;">Qty</th>
                <th>Harga</th>
                <th>Total</th>
                <th>Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dailySalesItems as $item)
                <tr>
                    <td>{{ $item['product'] }}</td>
                    <td>{{ $item['customer'] }}</td>
                    <td style="text-align:center;">{{ $item['qty'] }}</td>
                    <td>Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item['total'], 0, ',', '.') }}</td>
                    <td>{{ $item['payment'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;">Tidak ada penjualan</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Laporan Reservasi</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th style="text-align:center;">Tamu</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>DP</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($allReservations as $res)
                <tr>
                    <td>{{ $res->reservation_code }}</td>
                    <td>{{ $res->customer_name }}</td>
                    <td style="text-align:center;">{{ $res->guests }}</td>
                    <td>{{ substr($res->reservation_time, 0, 5) }} WIB</td>
                    <td>{{ $res->status_label }}</td>
                    <td>{{ $res->dp_status_label }}</td>
                    <td>{{ $res->total_amount_formatted }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;">Tidak ada reservasi</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ now()->translatedFormat('d M Y H:i') }}</div>
</body>
</html>