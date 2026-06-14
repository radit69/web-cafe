@extends('backend.admin.layout')

@section('title', 'Manajemen Customer')
@section('page_title', 'Manajemen Customer')

@section('styles')
    <style>
        .customer-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .customer-actions,
        .customer-filter {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .soft-card {
            background: #faecd8;
            border: 1px solid rgba(53, 64, 36, 0.08);
            border-radius: 8px;
            padding: 18px;
        }

        .customer-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .customer-stat {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 86px;
        }

        .customer-stat-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #dae8c0;
            color: #202a10;
            flex: 0 0 44px;
        }

        .customer-stat-label {
            font-size: 11px;
            color: #706144;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .customer-stat-value {
            margin-top: 2px;
            font-size: 25px;
            line-height: 1;
            font-weight: 700;
            color: #202a10;
        }

        .customer-input,
        .customer-select {
            height: 38px;
            border: 1px solid rgba(53, 64, 36, 0.12);
            background: #fff8f3;
            border-radius: 8px;
            padding: 0 12px;
            font-family: inherit;
            font-size: 13px;
            color: #211b0f;
            outline: none;
        }

        .customer-input {
            min-width: 240px;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(53, 64, 36, 0.1);
            border-radius: 8px;
            background: #fff8f3;
            color: #354024;
            text-decoration: none;
            cursor: pointer;
        }

        .icon-btn:hover {
            background: #dae8c0;
        }

        .icon-btn.danger {
            color: #ba1a1a;
        }

        .customer-table-wrap {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid rgba(53, 64, 36, 0.08);
            background: #fff8f3;
        }

        .customer-table th {
            background: #ead7b6;
            color: #211b0f;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .customer-table td {
            vertical-align: middle;
            border-bottom: 1px solid rgba(53, 64, 36, 0.06);
        }

        .customer-table tbody tr:nth-child(even) {
            background: rgba(250, 236, 216, 0.4);
        }

        .customer-name {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 220px;
        }

        .customer-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dae8c0;
            color: #202a10;
            font-weight: 700;
            overflow: hidden;
            flex: 0 0 38px;
        }

        .customer-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .customer-meta {
            display: block;
            color: #706144;
            font-size: 11px;
            margin-top: 2px;
        }

        .badge-muted {
            background: rgba(108, 92, 64, .13);
            color: #53452b;
        }

        .customer-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 14px;
            flex-wrap: wrap;
            color: #706144;
            font-size: 12px;
        }

        .pagination-mini {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 980px) {
            .customer-stats {
                grid-template-columns: repeat(2, minmax(160px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .customer-stats {
                grid-template-columns: 1fr;
            }

            .customer-input,
            .customer-select {
                width: 100%;
                min-width: 0;
            }

            .customer-actions,
            .customer-filter,
            .customer-toolbar {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="customer-stats">
        <div class="soft-card customer-stat">
            <span class="customer-stat-icon"><i class="fa-solid fa-users"></i></span>
            <div>
                <div class="customer-stat-label">Total Customer</div>
                <div class="customer-stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
            </div>
        </div>
        <div class="soft-card customer-stat">
            <span class="customer-stat-icon"><i class="fa-solid fa-user-check"></i></span>
            <div>
                <div class="customer-stat-label">Customer Aktif</div>
                <div class="customer-stat-value">{{ number_format($stats['active'] ?? 0) }}</div>
            </div>
        </div>
        <div class="soft-card customer-stat">
            <span class="customer-stat-icon" style="background:#f2ddb9;"><i class="fa-solid fa-calendar-plus"></i></span>
            <div>
                <div class="customer-stat-label">Baru Bulan Ini</div>
                <div class="customer-stat-value">{{ number_format($stats['new_this_month'] ?? 0) }}</div>
            </div>
        </div>
        <div class="soft-card customer-stat">
            <span class="customer-stat-icon" style="background:#ffdad6;color:#93000a;"><i class="fa-solid fa-user-slash"></i></span>
            <div>
                <div class="customer-stat-label">Nonaktif</div>
                <div class="customer-stat-value">{{ number_format($stats['inactive'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div style="margin-bottom:14px;padding:10px 14px;border-radius:8px;background:rgba(139,195,74,0.16);font-size:13px;color:#487b1f;">
            {{ session('success') }}
        </div>
    @endif

    <div class="soft-card">
        <div class="customer-toolbar">
            <div class="customer-actions">
                <a href="{{ route('admin.customers.create') }}" class="btn-pill">
                    <i class="fa-solid fa-user-plus" style="margin-right:8px;"></i>
                    Tambah Customer
                </a>
                <a href="{{ route('admin.customers.export') }}" class="icon-btn" title="Ekspor CSV">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>

            <form action="{{ route('admin.customers.index') }}" method="get" class="customer-filter">
                <select name="status" class="customer-select" aria-label="Filter status">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <input class="customer-input" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, telepon...">
                <button type="submit" class="icon-btn" title="Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </form>
        </div>

        <div class="customer-table-wrap">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Sumber</th>
                        <th>Login Terakhir</th>
                        <th>Status</th>
                        <th style="width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>
                                <div class="customer-name">
                                    <span class="customer-avatar">
                                        @if ($customer->avatar)
                                            <img src="{{ $customer->avatar }}" alt="{{ $customer->name }}">
                                        @else
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        @endif
                                    </span>
                                    <span>
                                        <strong>{{ $customer->name }}</strong>
                                        <span class="customer-meta">Terdaftar {{ optional($customer->created_at)->format('d M Y') }}</span>
                                    </span>
                                </div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?: '-' }}</td>
                            <td>
                                @if ($customer->google_id)
                                    <span class="badge badge-success">Google</span>
                                @else
                                    <span class="badge badge-muted">Manual</span>
                                @endif
                            </td>
                            <td>{{ $customer->last_login_at ? $customer->last_login_at->diffForHumans() : '-' }}</td>
                            <td>
                                @if ($customer->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="icon-btn" title="Edit customer">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.customers.toggle', $customer) }}" method="post" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="icon-btn" title="{{ $customer->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fa-solid {{ $customer->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.customers.destroy', $customer) }}" method="post" style="display:inline;" onsubmit="return confirm('Hapus customer ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn danger" title="Hapus customer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:24px;color:#706144;">
                                Belum ada customer yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="customer-footer">
            <span>
                Menampilkan {{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }} dari {{ number_format($customers->total()) }} customer
            </span>
            <div class="pagination-mini">
                @if ($customers->onFirstPage())
                    <span class="icon-btn" style="opacity:.45;cursor:not-allowed;"><i class="fa-solid fa-chevron-left"></i></span>
                @else
                    <a class="icon-btn" href="{{ $customers->previousPageUrl() }}" title="Sebelumnya"><i class="fa-solid fa-chevron-left"></i></a>
                @endif

                <span>Halaman {{ $customers->currentPage() }} / {{ $customers->lastPage() }}</span>

                @if ($customers->hasMorePages())
                    <a class="icon-btn" href="{{ $customers->nextPageUrl() }}" title="Berikutnya"><i class="fa-solid fa-chevron-right"></i></a>
                @else
                    <span class="icon-btn" style="opacity:.45;cursor:not-allowed;"><i class="fa-solid fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
    </div>
@endsection
