@extends('backend.admin.layout')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan Penjualan')

@section('content')
    @php
        use App\Models\Sale;
        use App\Models\Reservation;

        $from = request('from', today()->subDays(30)->toDateString());
        $to = request('to', today()->toDateString());

        // Ambil penjualan dari Sale (order kasir + cart frontend)
        $salesQuery = Sale::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        // Ambil penjualan dari Reservation yang sudah confirmed/completed
        $reservationsQuery = Reservation::whereIn('status', ['confirmed', 'completed'])
            ->whereDate('reservation_date', '>=', $from)
            ->whereDate('reservation_date', '<=', $to);

        $totalPenjualan = (clone $salesQuery)->sum('total') + (clone $reservationsQuery)->sum('total_amount');
        $jumlahPesanan = (clone $salesQuery)->count() + (clone $reservationsQuery)->count();
        $keuntungan = $totalPenjualan;

        // Data per tanggal
        $dailySales = [];
        $dates = [];
        $period = new DatePeriod(
            new DateTime($from),
            new DateInterval('P1D'),
            (new DateTime($to))->modify('+1 day')
        );
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $dates[] = $d;
            $saleTotal = (clone $salesQuery)->whereDate('created_at', $d)->sum('total');
            $resTotal = (clone $reservationsQuery)->whereDate('reservation_date', $d)->sum('total_amount');
            $total = $saleTotal + $resTotal;
            if ($total > 0) {
                $dailySales[$d] = [
                    'total' => $total,
                    'count' => (clone $salesQuery)->whereDate('created_at', $d)->count()
                        + (clone $reservationsQuery)->whereDate('reservation_date', $d)->count(),
                ];
            }
        }

        // Compile all detailed transactions for rincian transaksi table
        $salesList = (clone $salesQuery)->get()->map(function($sale) {
            return [
                'date' => $sale->created_at,
                'code' => $sale->code,
                'customer' => 'Umum (Kasir/Frontend)',
                'type' => 'Penjualan Langsung',
                'total' => $sale->total,
                'payment' => $sale->payment_method ?? 'Midtrans',
                'status' => $sale->payment_status ?? 'settlement'
            ];
        });

        $reservationsList = (clone $reservationsQuery)->get()->map(function($res) {
            return [
                'date' => $res->reservation_date->format('Y-m-d') . ' ' . $res->reservation_time,
                'code' => $res->reservation_code,
                'customer' => $res->customer_name,
                'type' => 'Reservasi',
                'total' => $res->total_amount,
                'payment' => 'Reservasi (DP: ' . strtoupper($res->dp_status) . ')',
                'status' => $res->status
            ];
        });

        $allTransactions = $salesList->concat($reservationsList)->sortByDesc('date');
    @endphp

    <div class="top-actions">
        <form action="{{ route('admin.sales.export') }}" method="GET">
            <button type="submit" class="btn-pill">Export</button>
        </form>
    </div>

    <div class="content-card">
        <form method="GET" action="{{ route('admin.sales') }}" style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <div style="background:#FBEAC3;border-radius:14px;padding:8px 12px;font-size:13px;display:flex;gap:6px;align-items:center;">
                <input
                    type="date"
                    name="from"
                    value="{{ $from }}"
                    style="border:none;background:transparent;font-size:12px;outline:none;"
                >
                <span style="font-size:12px;">s/d</span>
                <input
                    type="date"
                    name="to"
                    value="{{ $to }}"
                    style="border:none;background:transparent;font-size:12px;outline:none;"
                >
            </div>
            <button type="submit" class="btn-pill" style="padding:6px 14px;font-size:12px;">Terapkan</button>
        </form>

        <div class="card-row" style="margin-bottom:10px;">
            <div class="stat-card">
                <div class="stat-label">Total Penjualan</div>
                <div class="stat-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pesanan</div>
                <div class="stat-value">{{ $jumlahPesanan }}</div>
            </div>
        </div>

        <div style="margin-top:10px;font-size:13px;font-weight:600;margin-bottom:6px;">
            Tampilan Penjualan
        </div>

        <div style="border-radius:18px;overflow:hidden;background:#FBEAC3;">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Penjualan</th>
                        <th>Jumlah Order</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dailySales as $date => $data)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</td>
                            <td>Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                            <td>{{ $data['count'] }} pesanan</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 30px; color: #8B3A3A;">
                                Belum ada data penjualan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:24px;font-size:13px;font-weight:600;margin-bottom:6px;">
            Rincian Transaksi
        </div>

        <div style="border-radius:18px;overflow:hidden;background:#FBEAC3;">
            <table>
                <thead>
                    <tr>
                        <th>Waktu / Tanggal</th>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Total Pembayaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allTransactions as $tx)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($tx['date'])->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $tx['code'] }}</td>
                            <td>{{ $tx['customer'] }}</td>
                            <td>
                                <span class="badge" style="{{ $tx['type'] === 'Reservasi' ? 'background:rgba(139, 195, 74, 0.18); color:#4b8b1f;' : 'background:rgba(33,150,243,0.18); color:#0d47a1;' }}">
                                    {{ $tx['type'] }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($tx['total'], 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ in_array($tx['status'], ['settlement', 'confirmed', 'completed', 'paid', 'lunas']) ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($tx['status']) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #8B3A3A;">
                                Belum ada rincian transaksi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
