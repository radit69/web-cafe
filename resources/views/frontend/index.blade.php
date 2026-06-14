@extends('frontend.layout')

@section('title', 'coffith Coffee &amp; Kitchen')

@section('content')
    <!-- Hero Section -->
    <section class="relative h-[580px] flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover"
                alt="Eksterior coffith Coffee & Kitchen"
                src="{{ asset('images/coffith-exterior.jpg') }}" />
            <div class="absolute inset-0 bg-black/40"></div>
        </div>
        <div class="relative z-10 w-full px-container-margin-mobile md:px-container-margin-desktop max-w-[1280px] mx-auto text-on-primary">
            <div class="max-w-xl">
                <span class="font-label-sm text-label-sm uppercase tracking-[0.2em] mb-4 block">Selamat Datang di
                    coffith</span>
                <h1 class="font-display-lg text-display-lg md:text-[56px] leading-tight mb-6">Coffee &amp; Kitchen<br>dalam Satu Harmoni</h1>
                <p class="font-body-lg text-body-lg mb-10 opacity-90">Nikmati hidangan dan minuman pilihan dari dapur terbaik kami. Dari kopi artisan hingga makanan kekinian, semua ada untuk Anda.</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('frontend.menu') }}"
                        class="bg-primary-container text-on-primary-container px-8 py-4 rounded-lg font-label-md text-label-md hover:brightness-110 transition-all flex items-center gap-2">
                        Lihat Menu
                        <span class="material-symbols-outlined text-[20px]" data-icon="restaurant_menu">restaurant_menu</span>
                    </a>
                    <a href="{{ route('frontend.reservation') }}"
                        class="border border-on-primary text-on-primary px-8 py-4 rounded-lg font-label-md text-label-md hover:bg-white/10 transition-all">Reservasi Meja</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Pills -->
    <section class="py-8 px-container-margin-mobile md:px-container-margin-desktop max-w-[1280px] mx-auto fade-in">
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="{{ route('frontend.menu') }}"
                class="px-5 py-2 rounded-full font-label-sm text-label-sm bg-primary text-on-primary hover:brightness-110 transition-all">
                Semua Menu
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('frontend.menu', ['category' => $cat]) }}"
                    class="px-5 py-2 rounded-full font-label-sm text-label-sm bg-surface-container text-on-surface-variant hover:bg-primary hover:text-on-primary transition-all">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Menu Grid -->
    <section class="pb-section-gap px-container-margin-mobile md:px-container-margin-desktop max-w-[1280px] mx-auto fade-in">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="font-headline-lg text-headline-lg mb-2">Menu Pilihan</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Berbagai hidangan dan minuman favorit yang siap menemani hari Anda.</p>
            </div>
            <a class="text-primary font-label-md text-label-md border-b border-primary pb-1 flex items-center gap-2 flex-shrink-0"
                href="{{ route('frontend.menu') }}">Lihat Semua <span class="material-symbols-outlined"
                    data-icon="arrow_forward">arrow_forward</span></a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach ($menus->take(8) as $menu)
                <a href="{{ route('frontend.menu.detail', $menu) }}"
                    class="group bg-surface-container rounded-xl overflow-hidden hover-card">
                    <div class="aspect-square overflow-hidden bg-surface-container-high">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            src="{{ $menu->image_url }}"
                            alt="{{ $menu->name }}">
                    </div>
                    <div class="p-3 md:p-4">
                        <div class="font-label-sm text-label-sm text-on-surface-variant mb-1">{{ $menu->category ?? 'Umum' }}</div>
                        <h3 class="font-headline-sm text-[16px] md:text-[18px] font-semibold mb-1 truncate">{{ $menu->name }}</h3>
                        <div class="flex items-center justify-between">
                            <span class="font-label-md text-label-md text-primary font-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            @if ($menu->status === 'habis')
                                <span class="text-label-sm text-error font-medium">Habis</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- CTA Section -->
    <section class="pb-section-gap px-container-margin-mobile md:px-container-margin-desktop max-w-[1280px] mx-auto text-center fade-in">
        <div class="bg-primary text-on-primary py-16 px-8 rounded-3xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-20 opacity-10">
                <span class="material-symbols-outlined text-[300px]" data-icon="local_cafe">local_cafe</span>
            </div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <h2 class="font-headline-lg text-headline-lg mb-6">Siap Memesan?</h2>
                <p class="font-body-lg text-body-lg mb-10 opacity-80">Pesan menu favorit Anda sekarang atau reservasi meja untuk pengalaman bersantap yang lebih nyaman.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('frontend.menu') }}"
                        class="bg-surface text-primary px-10 py-4 rounded-lg font-label-md text-label-md hover:bg-surface-container transition-all">Pesan Sekarang</a>
                    <a href="{{ route('frontend.reservation') }}"
                        class="border border-on-primary text-on-primary px-10 py-4 rounded-lg font-label-md text-label-md hover:bg-white/10 transition-all">Reservasi Meja</a>
                </div>
            </div>
        </div>
    </section>
@endsection