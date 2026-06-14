@extends('backend.admin.layout')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@section('styles')
    <style>
        .chart-container {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 18px 20px 20px;
        }
        .chart-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-sub);
        }
        .chart-wrapper {
            position: relative;
            height: 260px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        @media (max-width: 900px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        .stat-card-icon {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card-icon .icon-round {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
        }
        .icon-green { background: #354024; }
        .icon-gold { background: #C8A951; }
        .icon-brown { background: #8B6B4A; }
        .icon-teal { background: #5A7A6B; }
    </style>
@endsection

@section('content')
    <div class="card-row">
        <div class="stat-card">
            <div class="stat-card-icon">
                <div>
                    <div class="stat-label">Penjualan Hari Ini</div>
                    <div class="stat-value">Rp {{ number_format($dailyRevenue ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="icon-round icon-green"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            </div>
            <div style="margin-top:8px;font-size:12px;color:var(--text-sub);">
                {{ number_format($dailyTransactionsCount ?? 0) }} transaksi
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">
                <div>
                    <div class="stat-label">Total Pengguna</div>
                    <div class="stat-value">{{ number_format($userCount ?? 0) }}</div>
                </div>
                <div class="icon-round icon-gold"><i class="fa-solid fa-users"></i></div>
            </div>
            <div style="margin-top:8px;font-size:12px;color:var(--text-sub);">
                terdaftar
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">
                <div>
                    <div class="stat-label">Menu Tersedia</div>
                    <div class="stat-value">{{ number_format($menuCount ?? 0) }}</div>
                </div>
                <div class="icon-round icon-brown"><i class="fa-solid fa-utensils"></i></div>
            </div>
            <div style="margin-top:8px;font-size:12px;color:var(--text-sub);">
                item aktif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">
                <div>
                    <div class="stat-label">Total Reservasi</div>
                    <div class="stat-value">{{ number_format($reservationCount ?? 0) }}</div>
                </div>
                <div class="icon-round icon-teal"><i class="fa-solid fa-calendar-check"></i></div>
            </div>
            <div style="margin-top:8px;font-size:12px;color:var(--text-sub);">
                semua status
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="chart-container">
            <div class="chart-title">Penjualan Mingguan</div>
            <div class="chart-wrapper">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
        <div class="chart-container">
            <div class="chart-title">Penjualan Bulanan</div>
            <div class="chart-wrapper">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        Chart.defaults.color = '#45483f';
        Chart.defaults.font.family = "'Poppins', sans-serif";

        // Weekly bar chart
        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($weekLabels) !!},
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: {!! json_encode($weekTotals) !!},
                    backgroundColor: '#354024',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return 'Rp' + v.toLocaleString('id-ID'); }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Monthly line chart
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthLabels) !!},
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: {!! json_encode($monthTotals) !!},
                    borderColor: '#354024',
                    backgroundColor: 'rgba(53,64,36,0.10)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#354024',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return 'Rp' + v.toLocaleString('id-ID'); }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });


    </script>
@endsection