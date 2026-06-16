<!DOCTYPE html>
<html class="light" lang="id">

<head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta charset="utf-8" />
        <title>@yield('title', 'Saji - Pengalaman Kuliner Eksklusif')</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
        <script id="tailwind-config">
                tailwind.config = {
                        darkMode: "class",
                        theme: {
                                extend: {
                                        "colors": {
                                                "on-error-container": "#93000a",
                                                "secondary-fixed-dim": "#d8c4a1",
                                                "error": "#ba1a1a",
                                                "surface-dim": "#e6d8c5",
                                                "on-error": "#ffffff",
                                                "tertiary-fixed-dim": "#c3cb9a",
                                                "on-tertiary-container": "#a4ac7d",
                                                "on-tertiary-fixed": "#181e00",
                                                "on-primary-fixed": "#151f06",
                                                "surface-container-lowest": "#ffffff",
                                                "surface-container-highest": "#efe0cd",
                                                "tertiary-fixed": "#dfe7b4",
                                                "primary-fixed-dim": "#becca6",
                                                "surface-variant": "#efe0cd",
                                                "inverse-primary": "#becca6",
                                                "outline-variant": "#c6c7bc",
                                                "error-container": "#ffdad6",
                                                "surface": "#fff8f3",
                                                "outline": "#76786e",
                                                "on-surface-variant": "#45483f",
                                                "on-surface": "#211b0f",
                                                "on-secondary-fixed": "#241a04",
                                                "on-primary": "#ffffff",
                                                "secondary-container": "#f2ddb9",
                                                "surface-container-high": "#f5e6d3",
                                                "surface-bright": "#fff8f3",
                                                "surface-tint": "#576343",
                                                "secondary-fixed": "#f5e0bc",
                                                "inverse-on-surface": "#fdefdb",
                                                "primary": "#202a10",
                                                "inverse-surface": "#372f22",
                                                "on-primary-container": "#9fac88",
                                                "on-secondary-fixed-variant": "#53452b",
                                                "primary-container": "#354024",
                                                "on-primary-fixed-variant": "#3f4b2e",
                                                "tertiary-container": "#39401b",
                                                "on-tertiary-fixed-variant": "#434a24",
                                                "on-secondary": "#ffffff",
                                                "background": "#fff8f3",
                                                "surface-container-low": "#fff2e1",
                                                "primary-fixed": "#dae8c0",
                                                "surface-container": "#faecd8",
                                                "on-tertiary": "#ffffff",
                                                "on-secondary-container": "#706144",
                                                "secondary": "#6c5c40",
                                                "tertiary": "#232a07",
                                                "bone": "#fff8f3"
                                        },
                                        "borderRadius": {
                                                "DEFAULT": "0.25rem",
                                                "lg": "0.5rem",
                                                "xl": "0.75rem",
                                                "full": "9999px"
                                        },
                                        "spacing": {
                                                "section-gap": "64px",
                                                "base": "8px",
                                                "gutter": "24px",
                                                "container-margin-desktop": "80px",
                                                "container-margin-mobile": "20px"
                                        },
                                        "fontFamily": {
                                                "headline-lg": ["Quicksand"],
                                                "body-md": ["Quicksand"],
                                                "headline-md": ["Quicksand"],
                                                "body-lg": ["Quicksand"],
                                                "display-lg": ["Quicksand"],
                                                "label-md": ["Quicksand"],
                                                "label-sm": ["Quicksand"],
                                                "headline-lg-mobile": ["Quicksand"]
                                        },
                                        "fontSize": {
                                                "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                                                "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                                                "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                                                "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                                                "display-lg": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                                                "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500" }],
                                                "label-sm": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                                                "headline-lg-mobile": ["28px", { "lineHeight": "36px", "fontWeight": "600" }]
                                        }
                                },
                        },
                }
        </script>
        <style>
                .material-symbols-outlined {
                        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
                }

                html {
                        scroll-behavior: smooth;
                }

                body {
                        padding-top: env(safe-area-inset-top);
                        padding-bottom: env(safe-area-inset-bottom);
                        padding-left: env(safe-area-inset-left);
                        padding-right: env(safe-area-inset-right);
                        background-color: #fff8f3;
                        color: #211b0f;
                        -webkit-font-smoothing: antialiased;
                }

                /* Animasi Fade In */
                .fade-in {
                        opacity: 0;
                        transform: translateY(30px);
                        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
                }

                .fade-in.visible {
                        opacity: 1;
                        transform: translateY(0);
                }

                /* Navbar Scroll Effect */
                .navbar-scrolled {
                        box-shadow: 0 4px 20px rgba(32, 42, 16, 0.1);
                }

                /* Hover Card Effect */
                .hover-card {
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                .hover-card:hover {
                        transform: translateY(-8px);
                        box-shadow: 0 12px 30px rgba(32, 42, 16, 0.15);
                }

                /* Button Pop Effect */
                .btn-pop {
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .btn-pop:hover {
                        transform: scale(1.05);
                }

                .btn-pop:active {
                        transform: scale(0.98);
                }
        </style>
</head>

<body class="font-body-md text-body-md overflow-x-hidden">
        <!-- TopNavBar -->
        <header class="bg-surface-container-low dark:bg-surface-dim sticky top-0 z-50 transition-colors duration-300 ease-in-out">
                <nav class="flex justify-between items-center w-full px-container-margin-mobile md:px-container-margin-desktop py-4 max-w-[1280px] mx-auto">
                        <div class="flex items-center gap-3">
                                <a href="{{ route('frontend.home') }}" class="flex flex-col items-start leading-tight">
                                        <span class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim">cOffith</span>
                                        <span class="font-label-sm text-label-sm text-secondary dark:text-on-surface-variant -mt-1">Coffee &amp; Kitchen</span>
                                </a>
                                <div class="relative inline-flex items-center gap-0.5 border-l border-outline-variant/40 pl-3">
                                        <span class="material-symbols-outlined text-[14px] text-secondary" data-icon="location_on">location_on</span>
                                        <button onclick="document.getElementById('location-dropdown').classList.toggle('hidden')" class="flex items-center gap-0.5 text-label-sm text-secondary hover:text-primary transition-colors cursor-pointer whitespace-nowrap">
                                                <span id="selected-location-text">Depok</span>
                                                <span class="material-symbols-outlined text-[14px]" data-icon="expand_more">expand_more</span>
                                        </button>
                                        <div id="location-dropdown" class="hidden absolute top-full left-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-outline-variant/30 py-2 z-50">
                                                <button onclick="selectLocation('depok')" class="w-full text-left px-4 py-2 font-label-sm text-on-surface-variant hover:bg-surface-container-highest hover:text-primary transition-colors" data-location="depok">Depok</button>
                                                <button onclick="selectLocation('cibubur')" class="w-full text-left px-4 py-2 font-label-sm text-on-surface-variant hover:bg-surface-container-highest hover:text-primary transition-colors" data-location="cibubur">Cibubur</button>
                                        </div>
                                </div>
                        </div>
                        <div class="hidden md:flex items-center gap-8">
                                <a class="font-body-md text-body-md text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-300 ease-in-out @if(request()->routeIs('frontend.home')) text-primary border-b-2 border-primary pb-1 @endif"
                                        href="{{ route('frontend.home') }}">Beranda</a>
                                <a class="font-body-md text-body-md text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-300 ease-in-out @if(request()->routeIs('frontend.menu')) text-primary border-b-2 border-primary pb-1 @endif"
                                        href="{{ route('frontend.menu') }}">Menu</a>
                                <a class="font-body-md text-body-md text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-300 ease-in-out @if(request()->routeIs('frontend.reservation')) text-primary border-b-2 border-primary pb-1 @endif"
                                        href="{{ route('frontend.reservation') }}">Reservasi</a>
                                <a class="font-body-md text-body-md text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-300 ease-in-out @if(request()->routeIs('frontend.status')) text-primary border-b-2 border-primary pb-1 @endif"
                                        href="{{ route('frontend.status') }}">Status</a>
                        </div>
                        <div class="flex items-center gap-4">
                                <a href="{{ route('frontend.cart') }}" class="relative">
                                    <span class="material-symbols-outlined text-primary hover:text-primary-fixed transition-colors duration-300" data-icon="shopping_bag">shopping_bag</span>
                                    <span id="cart-count" class="hidden absolute -top-2 -right-2 bg-error text-on-error text-[11px] font-bold min-w-[20px] h-[20px] flex items-center justify-center rounded-full px-1 shadow-sm">0</span>
                                </a>
                                @auth
                                    <div class="relative hidden md:flex items-center gap-2" id="user-menu">
                                        <span class="font-label-md text-primary">{{ auth()->user()->name }}</span>
                                        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-on-primary font-label-md text-sm cursor-pointer">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                        <div id="user-dropdown" class="absolute top-full right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-outline-variant/30 py-2 z-50 hidden">
                                            <p class="px-4 py-2 font-label-sm text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                                            <hr class="border-outline-variant/30">
                                            <a href="{{ route('frontend.profile') }}" class="w-full flex items-center gap-2 px-4 py-2 font-body-md text-on-surface-variant hover:bg-surface-container-highest hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                                Edit Profil
                                            </a>
                                            <form method="POST" action="{{ route('frontend.logout') }}">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 font-body-md text-error hover:bg-error/5 transition-colors flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                                    Keluar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('frontend.logout') }}" class="md:hidden">
                                        @csrf
                                        <button type="submit" class="material-symbols-outlined text-primary dark:text-primary-fixed-dim" data-icon="logout">logout</button>
                                    </form>
                                @else
                                    <button class="material-symbols-outlined text-primary dark:text-primary-fixed-dim hover:text-primary-fixed transition-colors" data-icon="person">person</button>
                                    <a href="{{ route('login') }}" class="hidden md:block bg-primary text-on-primary px-6 py-2 rounded-full font-label-md text-label-md active:scale-95 transition-transform">Masuk</a>
                                @endauth
                        </div>
                </nav>
        </header>

        @yield('content')

        <script>
                // Helper functions for cart
                function getCart() {
                        return JSON.parse(localStorage.getItem('pos_cart') || '[]');
                }

                function saveCart(cart) {
                        localStorage.setItem('pos_cart', JSON.stringify(cart));
                        updateCartBadge();
                }

                function updateCartBadge() {
                        const cart = getCart();
                        const count = cart.reduce((total, item) => total + item.qty, 0);
                        const badge = document.getElementById('cart-count');
                        if (badge) {
                                if (count > 0) {
                                        badge.textContent = count;
                                        badge.classList.remove('hidden');
                                } else {
                                        badge.classList.add('hidden');
                                }
                        }
                }

                // Dropdown user
                const userDropdown = document.getElementById('user-dropdown');
                const userMenuBtn = document.querySelector('#user-menu > div');
                if (userMenuBtn && userDropdown) {
                        userMenuBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                userDropdown.classList.toggle('hidden');
                        });
                        userDropdown.addEventListener('click', function(e) {
                                e.stopPropagation();
                        });
                        document.addEventListener('click', function() {
                                userDropdown.classList.add('hidden');
                        });
                }

                // Dropdown location & sync
                const savedLocation = localStorage.getItem('coffith_location') || 'depok';
                function selectLocation(loc) {
                        localStorage.setItem('coffith_location', loc);
                        const textSpan = document.getElementById('selected-location-text');
                        if (textSpan) {
                                textSpan.textContent = loc.charAt(0).toUpperCase() + loc.slice(1);
                        }
                        document.getElementById('location-dropdown').classList.add('hidden');
                        
                        // Sync form reservasi jika ada
                        var rsvLocation = document.getElementById('location');
                        if (rsvLocation) {
                                rsvLocation.value = loc;
                        }
                }
                
                // Initialize on load
                document.addEventListener('DOMContentLoaded', function() {
                        selectLocation(savedLocation);
                        updateCartBadge();

                        // Intersection Observer for fade-in animations
                        const fadeObserver = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                                entry.target.classList.add('visible');
                                                fadeObserver.unobserve(entry.target);
                                        }
                                });
                        }, { threshold: 0.05 });

                        document.querySelectorAll('.fade-in').forEach(el => {
                                fadeObserver.observe(el);
                        });
                });
        </script>
</body>
</html>