@extends('frontend.layout')

@section('title', 'Pesanan Berhasil | coffith Coffee &amp; Kitchen')

@section('content')
<main class="max-w-[600px] mx-auto px-container-margin-mobile md:px-container-margin-desktop py-section-gap text-center">
    @if ($sale && $sale->payment_status === 'settlement')
        <span class="material-symbols-outlined text-7xl text-primary mb-4" data-icon="check_circle">check_circle</span>
        <h1 class="font-display-lg text-display-lg text-primary mb-2">Pembayaran Berhasil!</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant mb-8">Pesanan kamu sudah tercatat.</p>
    @elseif ($sale)
        <span class="material-symbols-outlined text-7xl text-secondary mb-4" data-icon="hourglass">hourglass</span>
        <h1 class="font-display-lg text-display-lg text-primary mb-2">Menunggu Pembayaran</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant mb-8">Pembayaran sedang diproses.</p>
    @else
        <span class="material-symbols-outlined text-7xl text-primary mb-4" data-icon="shopping_bag">shopping_bag</span>
        <h1 class="font-display-lg text-display-lg text-primary mb-2">Pesanan Dibuat!</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant mb-8">Silakan selesaikan pembayaran di halaman sebelumnya.</p>
    @endif

    @if ($sale)
        <div class="bg-surface-container rounded-xl p-8 border border-secondary/10 text-left mb-8">
            <div class="flex justify-between mb-4">
                <span class="text-on-surface-variant font-body-md">Kode Pesanan</span>
                <span class="font-label-md text-primary">{{ $sale->code }}</span>
            </div>
            <div class="flex justify-between mb-4">
                <span class="text-on-surface-variant font-body-md">Status</span>
                <span class="font-label-md px-3 py-1 rounded-full {{ $sale->payment_status === 'settlement' ? 'bg-primary/10 text-primary' : 'bg-secondary/20 text-secondary' }}">
                    {{ $sale->payment_status === 'settlement' ? 'Lunas' : 'Menunggu' }}
                </span>
            </div>
            <div class="border-t border-secondary/10 pt-4 space-y-3">
                @foreach ($sale->items ?? [] as $item)
                    <div class="flex justify-between text-on-surface-variant font-body-md">
                        <div>
                            <span>{{ $item['name'] }} × {{ $item['qty'] }}</span>
                            @if(!empty($item['notes']))
                                <br><span style="font-size:12px; color:#888; font-style:italic;">Catatan: {{ $item['notes'] }}</span>
                            @endif
                        </div>
                        <span>Rp {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-secondary/10 pt-3 mt-3 space-y-1">
                <div class="flex justify-between text-on-surface-variant font-body-md text-sm">
                    <span>Pajak ({{ $pajak ?? 10 }}%)</span>
                    <span>Rp {{ number_format($sale->total - round($sale->total * 100 / (100 + ($pajak ?? 10) + ($service ?? 5))), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-label-md text-primary pt-2 border-t border-secondary/10">
                    <span>Total Dibayar</span>
                    <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-4">
        <a href="{{ route('frontend.menu') }}" class="bg-primary text-on-primary px-6 py-3 rounded-full font-label-md inline-block">
            Kembali ke Menu
        </a>
        <a href="{{ route('frontend.home') }}" class="text-primary underline font-label-md">
            Ke Beranda
        </a>
    </div>

    <script>
        localStorage.removeItem('pos_cart');
    </script>
</main>
@endsection
