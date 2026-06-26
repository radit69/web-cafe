@extends('frontend.layout')

@section('title', 'Menu Spesial | coffith Coffee &amp; Kitchen')

@section('content')
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <main class="max-w-[1280px] mx-auto px-container-margin-mobile md:px-container-margin-desktop pt-12 pb-32">
        <header class="mb-section-gap">
            <h2 class="font-headline-lg md:text-display-lg text-primary mb-6">Pilih menu spesial untuk kamu</h2>
        </header>

        <section class="mb-12 overflow-x-auto hide-scrollbar">
            <div class="flex items-center gap-3 min-w-max pb-2">
                <a
                    href="{{ route('frontend.menu') }}"
                    class="px-8 py-3 rounded-full font-label-md transition-all active:scale-95 {{ empty($category) ? 'bg-primary text-bone' : 'border border-secondary/30 text-secondary hover:bg-secondary/5' }}"
                >
                    Semua
                </a>

                @foreach ($categories as $item)
                    <a
                        href="{{ route('frontend.menu', ['category' => $item]) }}"
                        class="px-8 py-3 rounded-full font-label-md transition-all active:scale-95 {{ $category === $item ? 'bg-primary text-bone' : 'border border-secondary/30 text-secondary hover:bg-secondary/5' }}"
                    >
                        {{ $item }}
                    </a>
                @endforeach
            </div>
        </section>

        @if ($menus->isEmpty())
            <section class="bg-surface-container rounded-xl border border-secondary/10 p-10 text-center">
                <span class="material-symbols-outlined text-primary text-5xl mb-4" data-icon="restaurant_menu">restaurant_menu</span>
                <h3 class="font-headline-md text-headline-md text-primary mb-3">Menu Belum Tersedia</h3>
                <p class="text-on-surface-variant font-body-md max-w-xl mx-auto">
                </p>
            </section>
        @else
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter">
                @foreach ($menus as $menu)
                    @php
                        $isAvailable = $menu->status === 'tersedia' && $menu->stock > 0;
                    @endphp

                    <article class="group relative overflow-hidden bg-surface-container rounded-xl border border-secondary/10 transition-all duration-500 hover:shadow-xl">
                        <a href="{{ route('frontend.menu.detail', $menu) }}" class="block">
                            <div class="aspect-square overflow-hidden bg-surface-container-high">
                                <img
                                    alt="{{ $menu->name }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    src="{{ $menu->image_url }}"
                                    loading="lazy"
                                    onerror="this.parentElement.innerHTML='<div class=&quot;w-full h-full flex items-center justify-center bg-secondary/10 text-primary&quot;><span class=&quot;material-symbols-outlined text-6xl&quot; data-icon=&quot;restaurant&quot;>restaurant</span></div>'"
                                >
                            </div>
                        </a>

                        <div class="p-6">
                            <div class="flex justify-between items-start gap-3 mb-2">
                                <div>
                                    <p class="font-label-sm text-secondary mb-1">{{ $menu->category ?: 'Menu' }}</p>
                                    <a href="{{ route('frontend.menu.detail', $menu) }}" class="block">
                                        <h3 class="font-headline-md text-primary">{{ $menu->name }}</h3>
                                    </a>
                                </div>
                                <span class="font-label-sm px-2 py-1 rounded {{ $isAvailable ? 'text-primary bg-primary/10' : 'text-error bg-error/10' }}">
                                    {{ $isAvailable ? 'Tersedia' : 'Habis' }}
                                </span>
                            </div>



                            <div class="flex items-center justify-between gap-4">
                                <span class="font-headline-md text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                 <button
                                    class="add-cart-btn w-12 h-12 flex items-center justify-center rounded-full transition-all duration-300 {{ $isAvailable ? 'bg-outline/10 text-primary hover:bg-primary hover:text-bone cursor-pointer' : 'bg-outline/10 text-outline cursor-not-allowed' }}"
                                    type="button"
                                    {{ $isAvailable ? '' : 'disabled' }}
                                    aria-label="Tambah {{ $menu->name }}"
                                    data-id="{{ $menu->id }}"
                                    data-name="{{ $menu->name }}"
                                    data-price="{{ $menu->price }}"
                                    data-image="{{ $menu->image_url }}"
                                >
                                    <span class="material-symbols-outlined" data-icon="{{ $isAvailable ? 'add' : 'block' }}">{{ $isAvailable ? 'add' : 'block' }}</span>
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </main>

    <div id="floating-cart-banner" class="fixed bottom-20 md:bottom-8 left-1/2 -translate-x-1/2 w-[calc(100%-32px)] max-w-[500px] z-50 flex items-center justify-between p-3.5 bg-primary text-bone rounded-full shadow-2xl transition-all duration-300 transform translate-y-10 opacity-0 pointer-events-none">
        <div class="flex items-center gap-3 pl-2">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-bone">
                <span class="material-symbols-outlined text-xl" data-icon="shopping_bag">shopping_bag</span>
            </div>
            <div>
                <p class="font-body-md text-xs opacity-80 leading-none">Pesanan Anda</p>
                <p id="floating-cart-count" class="font-label-md text-sm font-bold mt-0.5">0 Item Terpilih</p>
            </div>
        </div>
        <a href="{{ route('frontend.cart') }}" class="bg-secondary-fixed text-on-secondary-fixed px-5 py-2.5 rounded-full font-label-md text-sm flex items-center gap-1.5 hover:brightness-105 active:scale-95 transition-all">
            Lanjut ke Keranjang
            <span class="material-symbols-outlined text-[16px]" data-icon="arrow_forward">arrow_forward</span>
        </a>
    </div>

    <nav
        class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-3 bg-surface-container dark:bg-inverse-surface rounded-t-xl md:hidden border-t border-outline/10">
        <a href="{{ route('frontend.home') }}" class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant">
            <span class="material-symbols-outlined" data-icon="home">home</span>
            <span class="font-label-sm text-label-sm">Beranda</span>
        </a>
        <a href="{{ route('frontend.menu') }}" class="flex flex-col items-center justify-center bg-primary-container dark:bg-primary text-on-primary-container dark:text-on-primary rounded-full px-6 py-1">
            <span class="material-symbols-outlined" data-icon="restaurant_menu">restaurant_menu</span>
            <span class="font-label-sm text-label-sm">Menu</span>
        </a>
        <a href="{{ route('frontend.reservation') }}" class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant">
            <span class="material-symbols-outlined" data-icon="event">event</span>
            <span class="font-label-sm text-label-sm">Reservasi</span>
        </a>
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center text-on-surface-variant dark:text-surface-variant">
            <span class="material-symbols-outlined" data-icon="person">person</span>
            <span class="font-label-sm text-label-sm">Akun</span>
        </a>
    </nav>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const banner = document.getElementById('floating-cart-banner');
    const bannerCount = document.getElementById('floating-cart-count');

    function updateFloatingBanner() {
        if (!banner || !bannerCount) return;
        const cart = JSON.parse(localStorage.getItem('pos_cart') || '[]');
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);

        if (totalItems > 0) {
            bannerCount.textContent = totalItems + ' Item Terpilih';
            banner.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
            banner.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        } else {
            banner.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
            banner.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }
    }

    // Initialize banner state
    updateFloatingBanner();

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.add-cart-btn');
        if (!btn || btn.disabled) return;
        var id = parseInt(btn.getAttribute('data-id'));
        var name = btn.getAttribute('data-name');
        var price = parseInt(btn.getAttribute('data-price'));
        var image = btn.getAttribute('data-image') || '';
        var cart = JSON.parse(localStorage.getItem('pos_cart') || '[]');
        var existing = null;
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) { existing = cart[i]; break; }
        }
        if (existing) { existing.qty += 1; }
        else { cart.push({ id: id, name: name, price: price, image: image, qty: 1, notes: '' }); }
        localStorage.setItem('pos_cart', JSON.stringify(cart));
        
        // Sync badge globally
        if (typeof updateCartBadge === 'function') {
            updateCartBadge();
        }

        // Show floating banner instead of redirecting
        updateFloatingBanner();
    });
});
</script>
@endsection
