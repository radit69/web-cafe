@extends('frontend.layout')

@section('title', 'Reservasi Meja | coffith Coffee &amp; Kitchen')

@section('content')
<style>
    .tonal-layer {
        background-color: rgba(207, 187, 153, 0.1);
    }
</style>
<!-- Hero Section -->
<section class="relative h-[409px] md:h-[512px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover brightness-50" src="{{ asset('images/coffith-bar.jpg') }}" alt="Bar coffith Coffee & Kitchen" />
    </div>
    <div class="relative z-10 text-center px-container-margin-mobile">
        <h1 class="font-display-lg text-display-lg text-white mb-4">Reservasi Meja</h1>
        <p class="font-body-lg text-body-lg text-surface-container-low max-w-2xl mx-auto">
            Pesan Sekarang dan Nikmati Kelezatannya!
Jangan lewatkan kesempatan untuk menikmati hidangan dan kopi terbaik kami.
        </p>
    </div>
</section>

<!-- Reservation Form Section -->
<section class="py-section-gap px-container-margin-mobile md:px-container-margin-desktop max-w-[1280px] mx-auto fade-in">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-stretch">
        <!-- Reservation Guidance -->
        <div class="lg:col-span-4 flex flex-col">
            <div class="p-8 bg-surface-container rounded-xl border border-secondary/20">
                <h2 class="font-headline-md text-headline-md text-primary mb-6">Informasi Reservasi</h2>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-secondary" data-icon="schedule">schedule</span>
                        <div>
                            <p class="font-label-md text-label-md text-primary">Jam Operasional</p>
                            <p class="font-body-md text-body-md text-on-surface-variant">Setiap Hari: 08:00 - 22:00</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-secondary" data-icon="group">group</span>
                        <div>
                            <p class="font-label-md text-label-md text-primary">Kapasitas Maksimal</p>
                            <p class="font-body-md text-body-md text-on-surface-variant">Reservasi online maksimal untuk 10 orang.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-secondary" data-icon="info">info</span>
                        <div>
                            <p class="font-label-md text-label-md text-primary">Kebijakan Pembatalan</p>
                            <p class="font-body-md text-body-md text-on-surface-variant">Pembatalan kurang dari 3 hari sebelum reservasi dikenakan charge Rp 50.000.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-secondary" data-icon="payments">payments</span>
                        <div>
                            <p class="font-label-md text-label-md text-primary">Pembayaran</p>
                            <p class="font-body-md text-body-md text-on-surface-variant">DP awal sebesar 50% dibayar segera setelah reservasi dikonfirmasi.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-secondary" data-icon="payments">payments</span>
                        <div>
                            <p class="font-label-md text-label-md text-primary">Pelunasan</p>
                            <p class="font-body-md text-body-md text-on-surface-variant">Sisa pembayaran dapat dilunasi H-1 atau pada hari H, secara online maupun di kasir.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="mt-6 flex-1 min-h-[220px] lg:min-h-0 relative">
                <img class="absolute inset-0 w-full h-full object-cover rounded-xl shadow-[0px_20px_40px_rgba(53,64,36,0.04)] border border-outline-variant/30" src="{{ asset('images/coffith-interior.jpg') }}" alt="Suasana Coffith">
            </div>
        </div>
        <!-- Form Canvas -->
        <div class="lg:col-span-8 flex flex-col space-y-12">
            @if (session('success'))
                <div class="p-4 bg-primary/10 border border-primary/20 rounded-xl text-primary font-body-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-4 bg-error/10 border border-error/20 rounded-xl text-error font-body-md">
                    <p class="font-label-md mb-2">Reservasi belum bisa dikirim:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="reservation-form" method="POST" action="{{ route('frontend.reservation.store') }}" class="flex-1 space-y-gutter p-gutter md:p-12 bg-white rounded-xl shadow-[0px_20px_40px_rgba(53,64,36,0.04)] border border-outline-variant/30">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="name">Nama Lengkap</label>
                        <input class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md" id="name" name="name" placeholder="Contoh: Budi Santoso" type="text" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="guests">Jumlah Orang</label>
                        <select class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md appearance-none" id="guests" name="guests">
                            @for ($guest = 1; $guest <= 10; $guest++)
                                <option value="{{ $guest }}" {{ (int) old('guests', 2) === $guest ? 'selected' : '' }}>{{ $guest }} Orang</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="email">Email</label>
                        <input class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md" id="email" name="email" placeholder="nama@email.com" type="email" value="{{ old('email', auth()->user()->email ?? '') }}">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="phone">Nomor Telepon</label>
                        <input class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md" id="phone" name="phone" placeholder="+62 812 3456 7890" type="text" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="location">Lokasi</label>
                        <select class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md appearance-none" id="location" name="location">
                            <option value="depok" {{ old('location') === 'depok' ? 'selected' : '' }}>Depok — Jl. K.H.M. Usman No.99, Kukusan, Kec. Beji</option>
                            <option value="cibubur" {{ old('location') === 'cibubur' ? 'selected' : '' }}>Cibubur — Jl. Raya Kranggan No.6, Jatiraden, Kec. Jatisampurna</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="date">Tanggal Kedatangan</label>
                        <input class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md" id="date" name="date" type="date" value="{{ old('date') }}" min="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter md:col-span-2">
                        <div class="space-y-2">
                            <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="time">Waktu</label>
                            <select class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md appearance-none" id="time" name="time">
                                <optgroup label="Waktu Tersedia">
                                    <option value="12:00" {{ old('time') === '12:00' ? 'selected' : '' }}>12:00</option>
                                    <option value="13:00" {{ old('time') === '13:00' ? 'selected' : '' }}>13:00</option>
                                    <option value="14:00" {{ old('time') === '14:00' ? 'selected' : '' }}>14:00</option>
                                    <option value="15:00" {{ old('time') === '15:00' ? 'selected' : '' }}>15:00</option>
                                    <option value="16:00" {{ old('time') === '16:00' ? 'selected' : '' }}>16:00</option>
                                    <option value="17:00" {{ old('time') === '17:00' ? 'selected' : '' }}>17:00</option>
                                    <option value="18:00" {{ old('time') === '18:00' ? 'selected' : '' }}>18:00</option>
                                    <option value="19:00" {{ old('time') === '19:00' ? 'selected' : '' }}>19:00</option>
                                    <option value="20:00" {{ old('time') === '20:00' ? 'selected' : '' }}>20:00</option>
                                    <option value="20:30" {{ old('time') === '20:30' ? 'selected' : '' }}>20:30</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="table_number">Meja</label>
                            <select class="w-full bg-transparent border-b border-outline-variant py-3 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md appearance-none" id="table_number" name="table_number">
                                <option value="">Pilih Meja</option>
                                @for ($t = 1; $t <= 15; $t++)
                                    <option value="{{ $t }}" {{ (int) old('table_number') === $t ? 'selected' : '' }}>Meja {{ $t }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="notes">Catatan Khusus (Opsional)</label>
                    <textarea class="w-full p-4 focus:outline-none focus:border-primary transition-colors font-body-md text-body-md bg-white border-b border-outline-variant rounded-none px-0" id="notes" name="notes" placeholder="Alergi makanan, perayaan ulang tahun, atau permintaan khusus lainnya..." rows="4">{{ old('notes') }}</textarea>
                </div>

                <!-- Hidden inputs for order items -->
                <div id="order-items-container"></div>
            </form>
        </div>

        <!-- Pilih Menu Section -->
        <div class="col-span-12">
            <h3 class="font-headline-md text-primary mb-6">Pilih Menu</h3>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse ($menus as $menu)
                        <div class="menu-item flex gap-3 p-3 bg-white rounded-xl border border-outline-variant/30" data-id="{{ $menu->id }}" data-name="{{ $menu->name }}" data-price="{{ $menu->price }}">
                            <img
                                alt="{{ $menu->name }}"
                                class="w-24 h-24 object-cover rounded-lg"
                                src="{{ $menu->image_url }}"
                                loading="lazy"
                                onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
                            >
                            <div class="w-24 h-24 rounded-lg bg-secondary/10 items-center justify-center text-primary" style="display:none">
                                <span class="material-symbols-outlined text-3xl" data-icon="restaurant">restaurant</span>
                            </div>
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-label-md text-primary">{{ $menu->name }}</h4>
                                    @if ($menu->category)
                                        <p class="font-label-sm text-on-surface-variant">{{ $menu->category }}</p>
                                    @endif
                                    <p class="font-headline-md text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                </div>
                                <button class="add-btn self-start px-4 py-1.5 bg-primary text-on-primary rounded-full text-label-sm" type="button">Tambah</button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 bg-surface-container rounded-xl border border-secondary/10 text-center">
                            <span class="material-symbols-outlined text-primary text-4xl mb-3" data-icon="restaurant_menu">restaurant_menu</span>
                            <p class="text-on-surface-variant font-body-md">Menu spesial belum tersedia. Silakan kembali lagi nanti.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Order Summary Sidebar -->
                <div class="md:col-span-4">
                    <div class="p-6 bg-surface-container rounded-xl border border-secondary/10 sticky top-24">
                        <h4 class="font-label-sm text-secondary uppercase tracking-widest mb-4">Ringkasan Pesanan</h4>
                        <div id="order-summary-items" class="space-y-3 mb-6">
                            <p id="empty-order-msg" class="text-on-surface-variant font-label-md">Belum ada item dipilih.</p>
                        </div>
                        <div class="border-t border-outline-variant/30 pt-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="font-body-md text-sm text-on-surface-variant">Pajak ({{ $pajak ?? 10 }}%)</span>
                                <span id="order-pajak-amount" class="font-body-md text-sm text-on-surface-variant">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-body-md text-sm text-on-surface-variant">Service ({{ $service ?? 5 }}%)</span>
                                <span id="order-service-amount" class="font-body-md text-sm text-on-surface-variant">Rp 0</span>
                            </div>
                            <div class="flex justify-between border-t border-outline-variant/30 pt-2">
                                <span class="font-body-md">Total</span>
                                <span id="order-total" class="font-headline-md text-primary">Rp 0</span>
                            </div>
                        </div>
                        <button class="w-full py-4 bg-primary text-on-primary font-label-md rounded-lg active:scale-95 transition-all shadow-lg" type="submit" form="reservation-form">
                            Konfirmasi Reservasi
                        </button>
                        <p class="mt-4 font-label-sm text-label-sm text-on-surface-variant text-center">
                            Sesuai dengan <a class="underline" href="#">Syarat &amp; Ketentuan</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderItems = {};

    const summaryContainer = document.getElementById('order-summary-items');
    const totalSpan = document.getElementById('order-total');
    const hiddenContainer = document.getElementById('order-items-container');

    function formatPrice(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }

    function renderSummary() {
        const entries = Object.entries(orderItems);

        summaryContainer.innerHTML = '';

        if (entries.length === 0) {
            const msg = document.createElement('p');
            msg.id = 'empty-order-msg';
            msg.className = 'text-on-surface-variant font-label-md';
            msg.textContent = 'Belum ada item dipilih.';
            summaryContainer.appendChild(msg);
            totalSpan.textContent = formatPrice(0);
            document.getElementById('order-pajak-amount').textContent = formatPrice(0);
            document.getElementById('order-service-amount').textContent = formatPrice(0);
            hiddenContainer.innerHTML = '';
            return;
        }

        let subtotalAmount = 0;

        entries.forEach(([id, item]) => {
            const lineTotal = item.qty * item.price;
            subtotalAmount += lineTotal;

            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2';
            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <p class="font-label-md text-primary truncate">${item.name}</p>
                    <p class="font-label-sm text-on-surface-variant">${item.qty} x ${formatPrice(item.price)}</p>
                </div>
                <div class="flex items-center gap-1">
                    <button class="qty-btn w-7 h-7 flex items-center justify-center rounded-full bg-primary/10 text-primary text-sm" data-id="${id}" data-action="minus">−</button>
                    <span class="w-5 text-center font-label-md">${item.qty}</span>
                    <button class="qty-btn w-7 h-7 flex items-center justify-center rounded-full bg-primary/10 text-primary text-sm" data-id="${id}" data-action="plus">+</button>
                    <button class="remove-btn w-7 h-7 flex items-center justify-center rounded-full bg-error/10 text-error text-sm ml-1" data-id="${id}">×</button>
                </div>
            `;
            summaryContainer.appendChild(row);
        });

        const pajakRate = {{ $pajak ?? 10 }};
        const serviceRate = {{ $service ?? 5 }};
        const pajakAmount = Math.round(subtotalAmount * pajakRate / 100);
        const serviceAmount = Math.round(subtotalAmount * serviceRate / 100);
        const total = subtotalAmount + pajakAmount + serviceAmount;

        document.getElementById('order-pajak-amount').textContent = formatPrice(pajakAmount);
        document.getElementById('order-service-amount').textContent = formatPrice(serviceAmount);
        totalSpan.textContent = formatPrice(total);

        const hiddenInputs = entries.map(([id, item]) => {
            return `<input type="hidden" name="order_items[${id}][id]" value="${id}">
                    <input type="hidden" name="order_items[${id}][name]" value="${item.name}">
                    <input type="hidden" name="order_items[${id}][qty]" value="${item.qty}">
                    <input type="hidden" name="order_items[${id}][price]" value="${item.price}">`;
        }).join('');
        hiddenContainer.innerHTML = hiddenInputs;

        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const action = this.dataset.action;
                if (action === 'plus') {
                    orderItems[id].qty++;
                } else if (action === 'minus') {
                    orderItems[id].qty--;
                    if (orderItems[id].qty <= 0) {
                        delete orderItems[id];
                    }
                }
                renderSummary();
            });
        });

        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                delete orderItems[this.dataset.id];
                renderSummary();
            });
        });
    }

    document.querySelectorAll('.add-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('.menu-item');
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseInt(card.dataset.price);

            if (orderItems[id]) {
                orderItems[id].qty++;
            } else {
                orderItems[id] = { name, price, qty: 1 };
            }

            renderSummary();
        });
    });

    // Sync lokasi dari navigasi
    const savedLocation = localStorage.getItem('coffith_location') || 'depok';
    const locationSelect = document.getElementById('location');
    if (locationSelect && !locationSelect.dataset.userChanged) {
        locationSelect.value = savedLocation;
    }
});
</script>
@endsection
