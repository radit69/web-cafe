@extends('frontend.layout')

@section('title', 'Status Reservasi | coffith Coffee &amp; Kitchen')

@section('content')
@php
    $statusBadge = [
        'pending' => 'bg-secondary/20 text-secondary',
        'confirmed' => 'bg-primary text-on-primary',
        'completed' => 'bg-tertiary-container/20 text-tertiary',
        'cancelled' => 'bg-error/15 text-error',
    ];
@endphp

<main class="pt-24 pb-section-gap px-container-margin-mobile md:px-container-margin-desktop max-w-[1280px] mx-auto">
    <header class="mb-12">
        <h1 class="font-display-lg text-display-lg text-primary mb-2 hidden md:block">Status Reservasi</h1>
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile text-primary mb-2 md:hidden">Status Reservasi</h1>
        <p class="text-on-surface-variant font-body-lg text-body-lg">Pantau jadwal dan detail kunjungan Anda di restoran pilihan kami</p>
    </header>

    @if (session('success'))
        <div class="mb-8 p-4 bg-primary/10 border border-primary/20 rounded-xl text-primary font-body-md">
            {{ session('success') }}
        </div>
    @endif

    @if (! $activeReservation)
        <section class="bg-surface-container-high rounded-xl p-8 border border-secondary/20 text-center">
            <h2 class="font-headline-md text-headline-md text-primary mb-3">Belum Ada Reservasi</h2>
            <p class="text-on-surface-variant font-body-md text-body-md mb-6">Reservasi yang Anda buat akan muncul di halaman ini.</p>
            <a href="{{ route('frontend.reservation') }}" class="inline-flex items-center justify-center bg-primary text-on-primary px-6 py-3 rounded-lg font-label-md">
                Buat Reservasi
            </a>
        </section>
    @else
        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
            <div class="md:col-span-8 bg-surface-container-high rounded-xl p-8 border border-secondary/20 flex flex-col md:flex-row gap-8 items-start relative">
                <div class="w-full md:w-1/3 aspect-square rounded-lg overflow-hidden shrink-0">
                    <img class="w-full h-full object-cover" src="{{ asset('images/coffith-exterior.jpg') }}" alt="cOffith Coffee & Kitchen" />
                </div>

                <div class="flex-1 space-y-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="font-headline-md text-headline-md text-primary">Reservasi #{{ $activeReservation->reservation_code }}</h3>
                        <span class="{{ $statusBadge[$activeReservation->status] ?? 'bg-secondary/20 text-secondary' }} px-4 py-2 rounded-full font-label-sm text-label-sm flex items-center gap-2 whitespace-nowrap">
                            <span class="material-symbols-outlined text-[16px]" data-icon="schedule_send">schedule_send</span>
                            {{ $activeReservation->status_label }}
                        </span>
                    </div>
                    <p class="text-on-surface-variant font-body-md text-body-md">Tanggal: {{ $activeReservation->date_label }}</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-surface-container rounded-lg p-4">
                            <p class="text-on-surface-variant font-label-sm text-label-sm mb-1">Waktu</p>
                            <p class="font-headline-md text-headline-md text-primary">{{ $activeReservation->time_label }}</p>
                        </div>
                        <div class="bg-surface-container rounded-lg p-4">
                            <p class="text-on-surface-variant font-label-sm text-label-sm mb-1">Jumlah Tamu</p>
                            <p class="font-headline-md text-headline-md text-primary">{{ $activeReservation->guests }} Orang</p>
                        </div>
                        <div class="bg-surface-container rounded-lg p-4">
                            <p class="text-on-surface-variant font-label-sm text-label-sm mb-1">Lokasi</p>
                            <p class="font-headline-md text-headline-md text-primary">{{ $activeReservation->location_label }}</p>
                        </div>
                        @if ($activeReservation->table_number)
                        <div class="bg-surface-container rounded-lg p-4">
                            <p class="text-on-surface-variant font-label-sm text-label-sm mb-1">Meja</p>
                            <p class="font-headline-md text-headline-md text-primary">Meja {{ $activeReservation->table_number }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-on-surface-variant">
                            <span class="material-symbols-outlined text-primary" data-icon="person">person</span>
                            <span class="font-body-md text-body-md">{{ $activeReservation->customer_name }}</span>
                        </div>
                        @if ($activeReservation->customer_email)
                            <div class="flex items-center gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined text-primary" data-icon="mail">mail</span>
                                <span class="font-body-md text-body-md">{{ $activeReservation->customer_email }}</span>
                            </div>
                        @endif
                        @if ($activeReservation->customer_phone)
                            <div class="flex items-center gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined text-primary" data-icon="local_phone">local_phone</span>
                                <span class="font-body-md text-body-md">{{ $activeReservation->customer_phone }}</span>
                            </div>
                        @endif
                        @if ($activeReservation->notes)
                            <div class="flex items-center gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined text-primary" data-icon="notes">notes</span>
                                <span class="font-body-md text-body-md">{{ $activeReservation->notes }}</span>
                            </div>
                        @endif
                        @if (!empty($activeReservation->order_items))
                            <div class="pt-3 border-t border-outline-variant/30">
                                <p class="font-label-sm text-secondary uppercase tracking-widest mb-2">Pesanan Menu</p>
                                @foreach ($activeReservation->order_items as $item)
                                    <div class="flex justify-between text-on-surface-variant font-body-md">
                                        <span>{{ $item['name'] }} × {{ $item['qty'] }}</span>
                                        <span>Rp {{ number_format($item['subtotal'] ?? ($item['qty'] * $item['price']), 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                                <div class="flex justify-between font-label-md text-primary pt-2 border-t border-outline-variant/20 mt-2">
                                    <span>Total</span>
                                    <span>Rp {{ number_format($activeReservation->total_amount, 0, ',', '.') }}</span>
                                </div>
                                @if ($activeReservation->dp_amount > 0)
                                    <div class="flex justify-between font-label-sm text-secondary pt-1">
                                        <span>DP (50%)</span>
                                        <span>Rp {{ number_format($activeReservation->dp_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between font-label-sm pt-1">
                                        <span>Sisa</span>
                                        @if ($activeReservation->dp_status === 'lunas')
                                            <span class="text-primary font-label-md">Lunas</span>
                                        @else
                                            <span>Rp {{ number_format($activeReservation->remaining_amount, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                @endif
                                @if ($activeReservation->cancellation_charge)
                                    <div class="flex justify-between font-label-sm text-error pt-1">
                                        <span>Charge Pembatalan</span>
                                        <span>Rp {{ number_format($activeReservation->cancellation_charge, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="md:col-span-4 space-y-6">
                <div class="bg-surface-container-low rounded-xl p-6 border border-secondary/10">
                    <h4 class="font-headline-md text-headline-md text-primary mb-6">Status Reservasi</h4>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-primary text-on-primary rounded-full flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]" data-icon="check_circle">check_circle</span>
                            </div>
                            <div>
                                <p class="font-label-md text-label-md text-primary">Diterima</p>
                                <p class="font-body-md text-body-md text-on-surface-variant">Reservasi Anda sudah masuk ke sistem.</p>
                                <p class="font-label-sm text-label-sm text-outline-variant mt-1">{{ $activeReservation->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 {{ in_array($activeReservation->status, ['confirmed', 'completed'], true) ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-outline-variant' }} rounded-full flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]" data-icon="{{ in_array($activeReservation->status, ['confirmed', 'completed'], true) ? 'check_circle' : 'pending' }}">
                                    {{ in_array($activeReservation->status, ['confirmed', 'completed'], true) ? 'check_circle' : 'pending' }}
                                </span>
                            </div>
                            <div>
                                <p class="font-label-md text-label-md {{ in_array($activeReservation->status, ['confirmed', 'completed'], true) ? 'text-primary' : 'text-outline-variant' }}">Dikonfirmasi</p>
                                <p class="font-body-md text-body-md text-on-surface-variant">
                                    @if ($activeReservation->table_number)
                                        Meja {{ $activeReservation->table_number }} sudah disiapkan untuk Anda.
                                    @else
                                        Tim kami akan menyiapkan meja sesuai jadwal.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DP Status Card --}}
                <div class="bg-surface-container-low rounded-xl p-6 border border-secondary/10">
                    <h4 class="font-headline-md text-headline-md text-primary mb-4">Pembayaran</h4>
                    <div class="space-y-3">
                        @if ($activeReservation->dp_amount > 0)
                            <div class="flex justify-between items-center">
                                <span class="font-label-md">DP (50%)</span>
                                <span class="font-label-md {{ $activeReservation->dp_status === 'paid' || $activeReservation->dp_status === 'lunas' ? 'text-primary' : 'text-error' }}">
                                    Rp {{ number_format($activeReservation->dp_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-label-md text-on-surface-variant">Status DP</span>
                                <span class="font-label-sm px-3 py-1 rounded-full {{ $activeReservation->dp_status === 'unpaid' ? 'bg-error/15 text-error' : ($activeReservation->dp_status === 'lunas' ? 'bg-primary/10 text-primary' : 'bg-secondary/20 text-secondary') }}">
                                    {{ $activeReservation->dp_status_label }}
                                </span>
                            </div>
                            @if ($activeReservation->dp_status === 'unpaid')
                                <button onclick="showDpModal()" class="w-full mt-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-label-md flex items-center justify-center gap-2 hover:bg-primary-fixed-dim transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="credit_card">credit_card</span>
                                    Bayar Pelunasan DP
                                </button>
                            @endif
                            @if ($activeReservation->dp_status === 'paid')
                                <div class="flex justify-between items-center pt-2 border-t border-outline-variant/20">
                                    <span class="font-label-md">Sisa Pembayaran</span>
                                    <span class="font-headline-md text-primary">Rp {{ number_format($activeReservation->remaining_amount, 0, ',', '.') }}</span>
                                </div>
                            @elseif ($activeReservation->dp_status === 'lunas')
                                <div class="flex justify-between items-center pt-2 border-t border-outline-variant/20">
                                    <span class="font-label-md">Sisa Pembayaran</span>
                                    <span class="font-headline-md text-primary">Lunas</span>
                                </div>
                            @endif
                        @else
                            <p class="text-on-surface-variant font-body-md">Tidak ada biaya reservasi.</p>
                        @endif
                    </div>
                </div>

                {{-- Remaining Payment Options (H-1 only) --}}
                @if ($activeReservation->dp_status === 'paid' && $activeReservation->remaining_amount > 0 && $isHMin1)
                <div class="bg-surface-container-low rounded-xl p-6 border border-secondary/10">
                    <h4 class="font-headline-md text-headline-md text-primary mb-4">Opsi Pelunasan</h4>
                    <p class="text-on-surface-variant font-body-md text-body-md mb-4">
                        Sisa pembayaran <strong>Rp {{ number_format($activeReservation->remaining_amount, 0, ',', '.') }}</strong> dapat dilunasi dengan:
                    </p>
                    <div class="space-y-3">
                        <button onclick="showInfoModal()" class="w-full bg-surface-container text-primary border border-primary/30 px-6 py-3 rounded-lg font-label-md flex items-center justify-center gap-2 hover:bg-primary/5 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]" data-icon="payments">payments</span>
                            Bayar di Kasir Langsung
                        </button>
                        <button onclick="payRemaining({{ $activeReservation->id }})" class="w-full bg-primary text-on-primary px-6 py-3 rounded-lg font-label-md flex items-center justify-center gap-2 hover:bg-primary-fixed-dim transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]" data-icon="qr_code_scanner">qr_code_scanner</span>
                            Bayar Sekarang (QRIS / M-Banking)
                        </button>
                    </div>
                </div>
                @endif

                <div class="space-y-4">
                    <div class="bg-surface-container rounded-xl p-4 border border-secondary/10">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary" data-icon="info">info</span>
                            <div>
                                <p class="font-label-md text-label-md text-primary">Catatan Penting</p>
                                <p class="font-body-md text-body-md text-on-surface-variant text-sm">Harap datang 10 menit sebelum waktu reservasi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-surface-container rounded-xl p-4 border border-secondary/10">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary" data-icon="cancel_presentation">cancel_presentation</span>
                            <div>
                                <p class="font-label-md text-label-md text-primary">Kebijakan Pembatalan</p>
                                <p class="font-body-md text-body-md text-on-surface-variant text-sm">Pembatalan kurang dari 3 hari sebelum reservasi dikenakan charge Rp 50.000.</p>
                            </div>
                        </div>
                    </div>

                    @if (in_array($activeReservation->status, ['pending', 'confirmed'], true))
                        <button onclick="showCancelModal()" class="w-full bg-error/15 text-error border border-error/30 px-6 py-3 rounded-lg font-label-md flex items-center justify-center gap-2 hover:bg-error/25 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]" data-icon="cancel">cancel</span>
                            Batalkan Reservasi
                        </button>
                    @endif
                </div>
            </div>

            {{-- DP Payment Options Modal --}}
            @if ($activeReservation->dp_status === 'unpaid' && $activeReservation->dp_amount > 0)
            <div id="dp-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                <div class="fixed inset-0 bg-black/50" onclick="hideDpModal()"></div>
                <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl">
                    <span class="material-symbols-outlined text-primary text-5xl block mb-4 text-center" data-icon="credit_card">credit_card</span>
                    <h3 class="font-headline-md text-headline-md text-center text-on-surface mb-2">Bayar DP Reservasi</h3>
                    <p class="font-body-md text-body-md text-center text-on-surface-variant mb-2">
                        Pembayaran DP sebesar
                    </p>
                    <p class="font-headline-md text-headline-md text-center text-primary mb-6">
                        Rp {{ number_format($activeReservation->dp_amount, 0, ',', '.') }}
                    </p>
                    <p class="font-body-md text-body-md text-center text-on-surface-variant mb-4">
                        Pilih metode pembayaran:
                    </p>
                    <div class="space-y-3">
                        <button onclick="hideDpModal(); payDp({{ $activeReservation->id }})" class="w-full bg-primary text-on-primary px-6 py-3 rounded-lg font-label-md flex items-center justify-center gap-2 hover:bg-primary-fixed-dim transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]" data-icon="qr_code_scanner">qr_code_scanner</span>
                            Bayar Sekarang (QRIS / M-Banking)
                        </button>
                        <button onclick="hideDpModal(); showDpKasirModal()" class="w-full bg-surface-container text-primary border border-primary/30 px-6 py-3 rounded-lg font-label-md flex items-center justify-center gap-2 hover:bg-primary/5 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]" data-icon="payments">payments</span>
                            Bayar di Kasir Langsung
                        </button>
                    </div>
                    <button type="button" onclick="hideDpModal()" class="w-full mt-3 text-on-surface-variant py-2 rounded-xl font-label-md hover:bg-surface-container transition-colors">Batal</button>
                </div>
            </div>

            {{-- DP Kasir Info Modal --}}
            <div id="dp-kasir-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                <div class="fixed inset-0 bg-black/50" onclick="hideDpKasirModal()"></div>
                <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl">
                    <span class="material-symbols-outlined text-primary text-5xl block mb-4 text-center" data-icon="payments">payments</span>
                    <h3 class="font-headline-md text-headline-md text-center text-on-surface mb-2">Bayar DP di Kasir</h3>
                    <p class="font-body-md text-body-md text-center text-on-surface-variant mb-6">
                        Silakan datang ke kasir restoran kami untuk melakukan pembayaran DP sebesar <strong>Rp {{ number_format($activeReservation->dp_amount, 0, ',', '.') }}</strong> secara langsung.
                    </p>
                    <button type="button" onclick="hideDpKasirModal()" class="w-full bg-primary text-on-primary py-3 rounded-xl font-label-md">Mengerti</button>
                </div>
            </div>
            @endif

            {{-- Info Modal for "Bayar di Kasir" (Sisa Pembayaran) --}}
            <div id="info-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                <div class="fixed inset-0 bg-black/50" onclick="hideInfoModal()"></div>
                <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl">
                    <span class="material-symbols-outlined text-primary text-5xl block mb-4 text-center" data-icon="payments">payments</span>
                    <h3 class="font-headline-md text-headline-md text-center text-on-surface mb-2">Bayar di Kasir</h3>
                    <p class="font-body-md text-body-md text-center text-on-surface-variant mb-6">
                        Silakan datang ke kasir restoran kami untuk melakukan pelunasan sisa pembayaran secara langsung.
                    </p>
                    <button type="button" onclick="hideInfoModal()" class="w-full bg-primary text-on-primary py-3 rounded-xl font-label-md">Mengerti</button>
                </div>
            </div>

            <div class="md:col-span-12 mt-8">
                <h3 class="font-headline-md text-headline-md text-primary mb-6">Riwayat Reservasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
                    @forelse ($pastReservations as $reservation)
                        <a href="{{ route('frontend.status', ['id' => $reservation->id]) }}" class="bg-surface-container rounded-xl p-6 border border-secondary/10 hover:border-primary/50 hover:shadow-md hover:bg-surface-container-high transition-all duration-300 block hover:-translate-y-1">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="font-label-sm text-label-sm text-outline-variant mb-1">{{ $reservation->date_label }}</p>
                                    <h4 class="font-headline-md text-headline-md text-primary">Reservasi #{{ $reservation->reservation_code }}</h4>
                                </div>
                                <span class="{{ $statusBadge[$reservation->status] ?? 'bg-secondary/20 text-secondary' }} px-3 py-1 rounded-full font-label-sm text-label-sm">{{ $reservation->status_label }}</span>
                            </div>
                            <div class="space-y-2 text-on-surface-variant">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="schedule">schedule</span>
                                    <span class="font-body-md text-body-md">{{ $reservation->time_label }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="group">group</span>
                                    <span class="font-body-md text-body-md">{{ $reservation->guests }} Orang</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="location_on">location_on</span>
                                    <span class="font-body-md text-body-md">{{ $reservation->location_label }}</span>
                                </div>
                                @if ($reservation->table_number)
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="chair">chair</span>
                                    <span class="font-body-md text-body-md">Meja {{ $reservation->table_number }}</span>
                                </div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-on-surface-variant font-body-md text-body-md">Belum ada riwayat reservasi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</main>

@if ($activeReservation)
{{-- Cancel Confirmation Modal --}}
<div id="cancel-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-black/50" onclick="hideCancelModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl">
        <span class="material-symbols-outlined text-error text-5xl block mb-4 text-center" data-icon="warning">warning</span>
        <h3 class="font-headline-md text-headline-md text-center text-on-surface mb-2">Yakin ingin membatalkan?</h3>
        @if ($willCharge)
            <p class="font-body-md text-body-md text-center text-on-surface-variant mb-2">
                Pembatalan kurang dari 3 hari sebelum reservasi akan dikenakan charge
            </p>
            <p class="font-headline-md text-headline-md text-center text-error mb-6">Rp 50.000</p>
        @else
            <p class="font-body-md text-body-md text-center text-on-surface-variant mb-6">Reservasi akan dibatalkan tanpa dikenakan biaya.</p>
        @endif
        <form action="{{ route('frontend.reservation.cancel', $activeReservation->id) }}" method="POST">
            @csrf
            <div class="flex gap-3">
                <button type="button" onclick="hideCancelModal()" class="flex-1 bg-surface-container-high text-on-surface py-3 rounded-xl font-label-md">Kembali</button>
                <button type="submit" class="flex-1 bg-error text-white py-3 rounded-xl font-label-md">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function showCancelModal() {
    document.getElementById('cancel-modal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancel-modal').classList.add('hidden');
}

function showDpModal() {
    document.getElementById('dp-modal').classList.remove('hidden');
}

function hideDpModal() {
    document.getElementById('dp-modal').classList.add('hidden');
}

function showDpKasirModal() {
    document.getElementById('dp-kasir-modal').classList.remove('hidden');
}

function hideDpKasirModal() {
    document.getElementById('dp-kasir-modal').classList.add('hidden');
}

function showInfoModal() {
    document.getElementById('info-modal').classList.remove('hidden');
}

function hideInfoModal() {
    document.getElementById('info-modal').classList.add('hidden');
}

var currentOrderId = null;

function confirmPaymentStatus(reservationId, type) {
    return fetch('/reservasi/' + reservationId + '/confirm-payment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ type: type, order_id: currentOrderId }),
    })
    .then(function(res) { return res.json(); })
    .catch(function() { /* ignore errors, just redirect */ });
}

function payDp(id) {
    fetch('/reservasi/' + id + '/bayar-dp', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.snap_token) {
            currentOrderId = data.order_id || null;
            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    currentOrderId = result.order_id || currentOrderId;
                    confirmPaymentStatus(id, 'dp').then(function() {
                        window.location.href = '{{ route('frontend.status') }}';
                    });
                },
                onPending: function(result) {
                    currentOrderId = result.order_id || currentOrderId;
                    confirmPaymentStatus(id, 'dp').then(function() {
                        window.location.href = '{{ route('frontend.status') }}';
                    });
                },
                onError: function(result) {
                    window.location.href = '{{ route('frontend.status') }}';
                },
                onClose: function() {
                    window.location.href = '{{ route('frontend.status') }}';
                },
            });
        } else if (data.error) {
            alert(data.error);
        }
    })
    .catch(function(err) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

function payRemaining(id) {
    fetch('/reservasi/' + id + '/bayar-sisa', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.snap_token) {
            currentOrderId = data.order_id || null;
            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    currentOrderId = result.order_id || currentOrderId;
                    confirmPaymentStatus(id, 'remaining').then(function() {
                        window.location.href = '{{ route('frontend.status') }}';
                    });
                },
                onPending: function(result) {
                    currentOrderId = result.order_id || currentOrderId;
                    confirmPaymentStatus(id, 'remaining').then(function() {
                        window.location.href = '{{ route('frontend.status') }}';
                    });
                },
                onError: function(result) {
                    window.location.href = '{{ route('frontend.status') }}';
                },
                onClose: function() {
                    window.location.href = '{{ route('frontend.status') }}';
                },
            });
        } else if (data.error) {
            alert(data.error);
        }
    })
    .catch(function(err) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

@if ($snapToken)
(function() {
    var autoPayReservationId = {{ $activeReservation ? $activeReservation->id : 'null' }};
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            currentOrderId = result.order_id || null;
            if (autoPayReservationId) {
                confirmPaymentStatus(autoPayReservationId, 'dp').then(function() {
                    window.location.href = '{{ route('frontend.status') }}';
                });
            } else {
                window.location.href = '{{ route('frontend.status') }}';
            }
        },
        onPending: function(result) {
            currentOrderId = result.order_id || null;
            if (autoPayReservationId) {
                confirmPaymentStatus(autoPayReservationId, 'dp').then(function() {
                    window.location.href = '{{ route('frontend.status') }}';
                });
            } else {
                window.location.href = '{{ route('frontend.status') }}';
            }
        },
        onError: function(result) {
            window.location.href = '{{ route('frontend.status') }}';
        },
        onClose: function() {
            window.location.href = '{{ route('frontend.status') }}';
        },
    });
})();
@endif
</script>
@endsection
