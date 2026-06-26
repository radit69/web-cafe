@extends('frontend.layout')

@section('title', $menu->name . ' | coffith Coffee &amp; Kitchen')

@section('content')
    @php
        $isAvailable = $menu->status === 'tersedia' && $menu->stock > 0;
    @endphp

    <main class="max-w-[1280px] mx-auto px-container-margin-mobile md:px-container-margin-desktop py-section-gap">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
            <aside class="hidden md:block md:col-span-3 space-y-10">
                <div class="space-y-4">
                    <h3 class="font-headline-md text-headline-md text-primary">Menu Lainnya</h3>
                    <div class="space-y-6">
                        @forelse ($topMenus as $topMenu)
                            <a href="{{ route('frontend.menu.detail', $topMenu) }}" class="flex gap-4 items-center group">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ $topMenu->image_url }}" alt="{{ $topMenu->name }}" loading="lazy">
                                </div>
                                <div>
                                    <h4 class="font-label-md text-label-md">{{ $topMenu->name }}</h4>
                                    <p class="text-on-surface-variant font-label-sm">Rp {{ number_format($topMenu->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        @empty
                            <p class="text-on-surface-variant font-body-md">Belum ada menu lain.</p>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="font-headline-md text-headline-md text-primary">Kategori</h3>
                    <div class="space-y-3">
                        @forelse ($categories as $category)
                            <a
                                href="{{ route('frontend.menu', ['category' => $category]) }}"
                                class="block font-body-md text-on-surface-variant hover:text-primary transition-colors"
                            >
                                {{ $category }}
                            </a>
                        @empty
                            <p class="text-on-surface-variant font-body-md">Belum ada kategori.</p>
                        @endforelse
                    </div>
                </div>
            </aside>

            <div class="md:col-span-9 space-y-8">
                <nav class="text-label-sm text-on-surface-variant flex items-center gap-2">
                    <a href="{{ route('frontend.home') }}" class="hover:text-primary transition-colors">Beranda</a>
                    <span class="material-symbols-outlined text-[16px]" data-icon="chevron_right">chevron_right</span>
                    <a href="{{ route('frontend.menu') }}" class="hover:text-primary transition-colors">Menu</a>
                    <span class="material-symbols-outlined text-[16px]" data-icon="chevron_right">chevron_right</span>
                    <span class="text-primary">{{ $menu->name }}</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">
                    <div class="space-y-4">
                        <div class="aspect-square rounded-xl overflow-hidden bg-surface-container-high">
                            <img
                                class="w-full h-full object-cover"
                                src="{{ $menu->image_url }}"
                                alt="{{ $menu->name }}"
                                loading="lazy"
                                onerror="this.style.display='none';this.parentElement.innerHTML+='<div class=&quot;w-full h-full flex items-center justify-center bg-secondary/10 text-primary&quot;><span class=&quot;material-symbols-outlined text-7xl&quot; data-icon=&quot;restaurant&quot;>restaurant</span></div>'"
                            >
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <span class="bg-surface-container-highest text-secondary px-3 py-1 rounded-full font-label-sm text-label-sm inline-block mb-3">
                                {{ $menu->category ?: 'Menu' }}
                            </span>
                            <h1 class="font-display-lg text-display-lg text-primary mb-2">{{ $menu->name }}</h1>
                            <p class="font-headline-md text-headline-md text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>

                        <p class="font-body-lg text-body-lg text-on-surface-variant">
                            {{ $menu->description ?: 'Tidak ada deskripsi untuk menu ini.' }}
                        </p>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-surface-container rounded-xl p-4">
                                <p class="text-on-surface-variant font-label-sm text-label-sm mb-1">Stok</p>
                                <p class="font-headline-md text-headline-md text-primary">{{ $menu->stock }}</p>
                            </div>
                            <div class="bg-surface-container rounded-xl p-4">
                                <p class="text-on-surface-variant font-label-sm text-label-sm mb-1">Status</p>
                                <p class="font-headline-md text-headline-md {{ $isAvailable ? 'text-primary' : 'text-error' }}">{{ $isAvailable ? 'Tersedia' : 'Habis' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button
                                id="add-to-cart-btn"
                                class="flex-1 px-8 py-4 rounded-xl font-headline-md text-headline-md transition-all active:scale-[0.98] flex items-center justify-center gap-2 {{ $isAvailable ? 'bg-primary text-on-primary hover:opacity-90 cursor-pointer' : 'bg-outline/10 text-outline cursor-not-allowed' }}"
                                type="button"
                                {{ $isAvailable ? '' : 'disabled' }}
                                data-id="{{ $menu->id }}"
                                data-name="{{ $menu->name }}"
                                data-price="{{ $menu->price }}"
                                data-image="{{ $menu->image_url }}"
                            >
                                <span class="material-symbols-outlined" data-icon="{{ $isAvailable ? 'shopping_cart' : 'block' }}">{{ $isAvailable ? 'shopping_cart' : 'block' }}</span>
                                {{ $isAvailable ? 'Tambah ke Keranjang' : 'Belum Tersedia' }}
                            </button>
                        </div>
                    </div>
                </div>

                <section class="pt-section-gap">
                    <h2 class="font-headline-md text-headline-md text-primary mb-6">Produk Serupa</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter">
                        @if ($relatedMenus->isEmpty())
                            <p class="text-on-surface-variant font-body-md">Belum ada produk serupa.</p>
                        @else
                            @foreach ($relatedMenus as $relatedMenu)
                            @php
                                $relatedAvailable = $relatedMenu->status === 'tersedia' && $relatedMenu->stock > 0;
                            @endphp
                            <article class="group relative overflow-hidden bg-surface-container rounded-xl border border-secondary/10 transition-all duration-500 hover:shadow-xl">
                                <a href="{{ route('frontend.menu.detail', $relatedMenu) }}" class="block">
                                    <div class="aspect-square overflow-hidden bg-surface-container-high">
                                        <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" src="{{ $relatedMenu->image_url }}" alt="{{ $relatedMenu->name }}" loading="lazy">
                                    </div>
                                </a>
                                <div class="p-6">
                                    <p class="font-label-sm text-secondary mb-1">{{ $relatedMenu->category ?: 'Menu' }}</p>
                                    <a href="{{ route('frontend.menu.detail', $relatedMenu) }}">
                                        <h3 class="font-headline-md text-primary mb-1">{{ $relatedMenu->name }}</h3>
                                    </a>
                                    <div class="flex items-center justify-between">
                                        <span class="font-headline-md text-primary">Rp {{ number_format($relatedMenu->price, 0, ',', '.') }}</span>
                                        <span class="font-label-sm px-2 py-1 rounded {{ $relatedAvailable ? 'text-primary bg-primary/10' : 'text-error bg-error/10' }}">
                                            {{ $relatedAvailable ? 'Tersedia' : 'Habis' }}
                                        </span>
                                    </div>
                                </div>
                            </article>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('add-to-cart-btn');
        if (!btn || btn.disabled) return;
        btn.addEventListener('click', function () {
            var id = parseInt(this.getAttribute('data-id'));
            var name = this.getAttribute('data-name');
            var price = parseInt(this.getAttribute('data-price'));
            var image = this.getAttribute('data-image') || '';
            var cart = JSON.parse(localStorage.getItem('pos_cart') || '[]');
            var existing = null;
            for (var i = 0; i < cart.length; i++) {
                if (cart[i].id === id) { existing = cart[i]; break; }
            }
            if (existing) { existing.qty += 1; }
            else { cart.push({ id: id, name: name, price: price, image: image, qty: 1, notes: '' }); }
            localStorage.setItem('pos_cart', JSON.stringify(cart));
            window.location.href = '/keranjang';
        });
    });
    </script>
@endsection
