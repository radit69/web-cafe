@php
    $statusFilters = [
        'all' => 'Semua',
        'pending' => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    $currentStatus = $currentStatus ?? request('status', 'all');
    $currentDate = $currentDate ?? request('date');
    $reservations = collect($reservations ?? []);
    $stats = $stats ?? [
        'occupancy' => 0,
        'new_today' => 0,
        'available_tables' => 15,
        'total_tables' => 15,
    ];

    $statusClasses = [
        'pending' => 'status-pending',
        'confirmed' => 'status-confirmed',
        'completed' => 'status-completed',
        'cancelled' => 'status-cancelled',
    ];

    $selectedDateLabel = $currentDate
        ? \Carbon\Carbon::parse($currentDate)->translatedFormat('d F Y')
        : null;
@endphp

<div class="reservations-page">
    <div class="reservation-toolbar">
        <nav class="reservation-tabs" aria-label="Filter status reservasi">
            @foreach ($statusFilters as $key => $label)
                @php
                    $query = request()->query();
                    unset($query['status']);

                    if ($key !== 'all') {
                        $query['status'] = $key;
                    }

                    $filterUrl = request()->url() . (count($query) ? '?' . http_build_query($query) : '');
                @endphp
                <a href="{{ $filterUrl }}" class="reservation-tab {{ $currentStatus === $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <form class="reservation-date-form" method="GET" action="{{ request()->url() }}">
            @if ($currentStatus !== 'all')
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif

            <label class="reservation-date-btn">
                <i class="fa-solid fa-calendar-day"></i>
                <span>{{ $selectedDateLabel ?? 'Pilih Tanggal' }}</span>
                <input
                    class="reservation-date-input"
                    type="date"
                    name="date"
                    value="{{ $currentDate }}"
                    aria-label="Pilih tanggal reservasi"
                    data-date-picker
                >
            </label>

            @if ($currentDate)
                <a class="reservation-date-reset" href="{{ request()->url() }}{{ $currentStatus !== 'all' ? '?status=' . urlencode($currentStatus) : '' }}">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <section class="reservation-card">
        <div class="reservation-card-header">
            <h2 class="reservation-card-title">Daftar Reservasi {{ $selectedDateLabel ?? 'Hari Ini' }}</h2>
            <span class="reservation-count">{{ $reservations->count() }} Reservasi</span>
        </div>

        <div class="reservation-table-wrap">
            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Jadwal</th>
                        <th>Meja</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Catatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        @php($detailId = 'reservation-detail-' . $reservation->id)
                        <tr>
                            <td>
                                <div class="reservation-customer">
                                    <span class="reservation-avatar">
                                        {{ $reservation->initials }}
                                    </span>
                                    <div>
                                        <p class="reservation-primary">{{ $reservation->customer_name }}</p>
                                        <p class="reservation-muted">{{ $reservation->customer_email ?: ($reservation->customer_phone ?: 'Kontak belum diisi') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="reservation-primary">{{ $reservation->date_label }}</p>
                                <p class="reservation-muted">{{ $reservation->time_label }}</p>
                            </td>
                            <td>
                                @if ($reservation->table_number)
                                    <span class="reservation-capacity">
                                        <i class="fa-solid fa-chair"></i>
                                        Meja {{ $reservation->table_number }}
                                    </span>
                                @else
                                    <span class="reservation-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="reservation-capacity">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $reservation->location_label }}
                                </span>
                            </td>
                            <td>
                                <span class="reservation-capacity">
                                    <i class="fa-solid fa-users"></i>
                                    {{ $reservation->guests }} Orang
                                </span>
                            </td>
                            <td>
                                <p class="reservation-primary">Reservasi meja</p>
                                <p class="reservation-muted">{{ $reservation->notes ?: 'Tidak ada catatan' }}</p>
                            </td>
                            <td>
                                <span class="reservation-status {{ $statusClasses[$reservation->status] ?? 'status-pending' }}">
                                    {{ $reservation->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="reservation-actions">
                                    <button class="reservation-detail-btn" type="button" data-modal-open="{{ $detailId }}">
                                        <i class="fa-solid fa-eye"></i>
                                        <span>Detail</span>
                                    </button>
                                    <button class="reservation-delete-btn" type="button" data-delete-id="{{ $reservation->id }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="reservation-empty" colspan="8">Belum ada reservasi tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="reservation-card-footer">
            <p class="reservation-footnote">Menampilkan {{ $reservations->count() }} reservasi dari database.</p>
        </div>
    </section>

    <section class="reservation-stats" aria-label="Ringkasan reservasi">
        <article class="reservation-stat occupancy">
            <p class="reservation-stat-label">Tingkat Okupansi</p>
            <p class="reservation-stat-value">{{ $stats['occupancy'] }}%</p>
            <p class="reservation-stat-note">Berdasarkan tamu reservasi hari ini</p>
        </article>

        <article class="reservation-stat new">
            <p class="reservation-stat-label">Reservasi Baru</p>
            <p class="reservation-stat-value">{{ str_pad((string) $stats['new_today'], 2, '0', STR_PAD_LEFT) }}</p>
        </article>

        <article class="reservation-stat available">
            <p class="reservation-stat-label">Meja Tersedia</p>
            <p class="reservation-stat-value">{{ str_pad((string) $stats['available_tables'], 2, '0', STR_PAD_LEFT) }} <span>/ {{ $stats['total_tables'] }}</span></p>
            <p class="reservation-stat-note">Dihitung dari reservasi aktif hari ini</p>
        </article>
    </section>
</div>

@foreach ($reservations as $reservation)
    @php($detailId = 'reservation-detail-' . $reservation->id)

    <div class="reservation-modal" id="{{ $detailId }}" aria-hidden="true">
        <div class="reservation-modal-backdrop" data-modal-close></div>
        <section class="reservation-modal-panel" role="dialog" aria-modal="true" aria-labelledby="{{ $detailId }}-title">
            <div class="reservation-modal-header">
                <div>
                    <p class="reservation-modal-kicker">Detail Reservasi</p>
                    <h3 id="{{ $detailId }}-title">{{ $reservation->customer_name }}</h3>
                    <p>{{ $reservation->date_label }} - {{ $reservation->time_label }}</p>
                </div>
                <button class="reservation-modal-close" type="button" data-modal-close aria-label="Tutup detail reservasi">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="reservation-order-list">
                <div class="reservation-order-item">
                    <div>
                        <p class="reservation-primary">Kode Reservasi</p>
                        <p class="reservation-muted">{{ $reservation->reservation_code }}</p>
                    </div>
                    <span>{{ $reservation->status_label }}</span>
                </div>
                <div class="reservation-order-item">
                    <div>
                        <p class="reservation-primary">Kontak</p>
                        <p class="reservation-muted">{{ $reservation->customer_email ?: '-' }}{{ $reservation->customer_phone ? ' / ' . $reservation->customer_phone : '' }}</p>
                    </div>
                    <span>{{ $reservation->guests }}x</span>
                </div>
                <div class="reservation-order-item">
                    <div>
                        <p class="reservation-primary">Meja</p>
                        <p class="reservation-muted">{{ $reservation->table_label ?: 'Belum ditentukan' }}</p>
                    </div>
                    @if ($reservation->table_number)
                        <span><i class="fa-solid fa-chair"></i> {{ $reservation->table_number }}</span>
                    @endif
                </div>
                <div class="reservation-order-item">
                    <div>
                        <p class="reservation-primary">Lokasi</p>
                        <p class="reservation-muted">{{ $reservation->location_label }}</p>
                    </div>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $reservation->location_label }}</span>
                </div>
                <div class="reservation-order-item">
                    <div>
                        <p class="reservation-primary">Catatan</p>
                        <p class="reservation-muted">{{ $reservation->notes ?: 'Tidak ada catatan khusus' }}</p>
                    </div>
                </div>
                @if ($reservation->order_items && count($reservation->order_items) > 0)
                    <div class="reservation-order-items">
                        <p class="reservation-primary" style="margin-bottom: 8px;">Pesanan Menu</p>
                        @foreach ($reservation->order_items as $item)
                            <div class="reservation-order-item">
                                <div>
                                    <p class="reservation-primary">{{ $item['name'] }} <span class="reservation-muted">x{{ $item['qty'] }}</span></p>
                                    <p class="reservation-muted">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="reservation-order-item" style="border-top: 1px solid rgba(53, 64, 36, 0.08); padding-top: 8px;">
                            <div>
                                <p class="reservation-primary">Total Pesanan</p>
                            </div>
                            <span style="font-weight: 700;">Rp {{ number_format($reservation->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($reservation->dp_amount > 0)
                            <div class="reservation-order-item">
                                <div>
                                    <p class="reservation-primary">DP (50%)</p>
                                    <p class="reservation-muted">{{ $reservation->dp_status_label }}</p>
                                </div>
                                <span>Rp {{ number_format($reservation->dp_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="reservation-order-item">
                                <div>
                                    <p class="reservation-primary">Sisa Pembayaran</p>
                                </div>
                                @if ($reservation->dp_status === 'lunas')
                                    <span class="text-primary" style="font-weight: 600;">Lunas</span>
                                @else
                                    <span>Rp {{ number_format($reservation->remaining_amount, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="reservation-modal-footer" style="display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                <button class="reservation-modal-delete" type="button" data-delete-id="{{ $reservation->id }}">
                    <i class="fa-solid fa-trash-can"></i>
                    Hapus Reservasi
                </button>

                @if (!in_array($reservation->status, ['completed', 'cancelled'], true))
                    <div style="display: flex; gap: 8px;">
                        @if ($reservation->dp_status !== 'lunas')
                            <form action="{{ request()->routeIs('admin.*') ? route('admin.reservations.update_status', $reservation->id) : route('kasir.reservations.update_status', $reservation->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="dp_status" value="lunas">
                                <button type="submit" class="reservation-detail-btn btn-lunasi">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Lunasi Pembayaran
                                </button>
                            </form>
                        @endif

                        @if ($reservation->status !== 'completed')
                            <form action="{{ request()->routeIs('admin.*') ? route('admin.reservations.update_status', $reservation->id) : route('kasir.reservations.update_status', $reservation->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="reservation-detail-btn btn-selesaikan">
                                    <i class="fa-solid fa-check"></i>
                                    Selesaikan
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    </div>
@endforeach

{{-- Delete Confirmation Modal --}}
<div class="reservation-modal" id="delete-modal" aria-hidden="true">
    <div class="reservation-modal-backdrop" data-modal-close></div>
    <section class="reservation-modal-panel" role="dialog" aria-modal="true">
        <div class="reservation-modal-header">
            <div>
                <p class="reservation-modal-kicker">Konfirmasi Hapus</p>
                <h3>Hapus Reservasi</h3>
                <p>Yakin ingin menghapus reservasi ini? Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <button class="reservation-modal-close" type="button" data-modal-close aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="reservation-order-list" style="text-align: center; padding: 32px 24px;">
            <i class="fa-solid fa-trash-can" style="font-size: 48px; color: #ba1a1a; margin-bottom: 16px;"></i>
            <p style="color: var(--text-sub); font-size: 14px;">Reservasi akan dihapus permanen dari sistem.</p>
        </div>
        <div class="reservation-modal-footer" style="display: flex; gap: 12px;">
            <button class="reservation-detail-btn" type="button" data-modal-close style="flex: 1; justify-content: center;">Batal</button>
            <form id="delete-form" method="POST" style="flex: 1;">
                @csrf
                @method('DELETE')
                <button class="reservation-modal-delete" type="submit" style="width: 100%; justify-content: center;">Ya, Hapus</button>
            </form>
        </div>
    </section>
</div>

<script>
    var deleteModal = document.getElementById('delete-modal');
    var deleteForm = document.getElementById('delete-form');
    var routePrefix = '{{ request()->routeIs("admin.*") ? "admin" : "kasir" }}';

    document.querySelectorAll('[data-modal-open]').forEach((button) => {
        button.addEventListener('click', () => {
            var modal = document.getElementById(button.dataset.modalOpen);
            if (!modal) return;

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => {
            var modal = button.closest('.reservation-modal');
            if (!modal) return;

            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        document.querySelectorAll('.reservation-modal.open').forEach((modal) => {
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
        });
    });

    document.querySelectorAll('[data-date-picker]').forEach((input) => {
        input.addEventListener('change', () => input.form.submit());
    });

    document.querySelectorAll('[data-delete-id]').forEach((button) => {
        button.addEventListener('click', function () {
            var id = this.dataset.deleteId;
            deleteForm.action = '/' + routePrefix + '/reservations/' + id;
            deleteModal.classList.add('open');
            deleteModal.setAttribute('aria-hidden', 'false');
        });
    });
</script>
