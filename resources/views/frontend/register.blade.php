<!DOCTYPE html>

<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Daftar | coffith Coffee &amp; Kitchen</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-highest": "#efe0cd",
                        "on-secondary-fixed-variant": "#53452b",
                        "on-primary-fixed": "#151f06",
                        "on-tertiary-fixed": "#181e00",
                        "background": "#fff8f3",
                        "outline": "#76786e",
                        "on-tertiary-fixed-variant": "#434a24",
                        "on-secondary-container": "#706144",
                        "on-secondary-fixed": "#241a04",
                        "tertiary-fixed": "#dfe7b4",
                        "primary-container": "#354024",
                        "on-tertiary": "#ffffff",
                        "on-background": "#211b0f",
                        "inverse-primary": "#becca6",
                        "on-primary": "#ffffff",
                        "surface-container": "#faecd8",
                        "primary": "#202a10",
                        "error-container": "#ffdad6",
                        "secondary": "#6c5c40",
                        "secondary-fixed-dim": "#d8c4a1",
                        "on-primary-fixed-variant": "#3f4b2e",
                        "on-surface": "#211b0f",
                        "on-error-container": "#93000a",
                        "surface-bright": "#fff8f3",
                        "secondary-container": "#f2ddb9",
                        "surface-container-high": "#f5e6d3",
                        "secondary-fixed": "#f5e0bc",
                        "error": "#ba1a1a",
                        "on-secondary": "#ffffff",
                        "tertiary-fixed-dim": "#c3cb9a",
                        "surface": "#fff8f3",
                        "inverse-on-surface": "#fdefdb",
                        "outline-variant": "#c6c7bc",
                        "tertiary-container": "#39401b",
                        "surface-container-lowest": "#ffffff",
                        "tertiary": "#232a07",
                        "surface-tint": "#576343",
                        "surface-variant": "#efe0cd",
                        "surface-dim": "#e6d8c5",
                        "on-error": "#ffffff",
                        "surface-container-low": "#fff2e1",
                        "primary-fixed": "#dae8c0",
                        "on-primary-container": "#9fac88",
                        "on-tertiary-container": "#a4ac7d",
                        "primary-fixed-dim": "#becca6",
                        "inverse-surface": "#372f22",
                        "on-surface-variant": "#45483f"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "container-margin-mobile": "20px",
                        "container-margin-desktop": "80px",
                        "base": "8px",
                        "section-gap": "64px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Quicksand"],
                        "headline-lg-mobile": ["Quicksand"],
                        "body-lg": ["Quicksand"],
                        "label-md": ["Quicksand"],
                        "headline-md": ["Quicksand"],
                        "display-lg": ["Quicksand"],
                        "body-md": ["Quicksand"],
                        "label-sm": ["Quicksand"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "headline-lg-mobile": ["28px", { "lineHeight": "36px", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                        "display-lg": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "label-sm": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }]
                    }
                },
            },
        }
    </script>
    <style>
        body {
            background-color: #fff8f3;
            color: #211b0f;
            -webkit-font-smoothing: antialiased;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <header class="w-full top-0 sticky bg-surface z-50">
        <div class="flex justify-between items-center px-gutter py-4 max-w-[1280px] mx-auto">
            <a href="{{ route('frontend.home') }}" class="flex flex-col items-start leading-tight">
                <span
                    class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim">cOffith</span>
                <span class="font-label-sm text-label-sm text-secondary dark:text-on-surface-variant -mt-1">Coffee
                    &amp; Kitchen</span>
            </a>
            <nav class="hidden md:flex gap-8">
                <a class="text-on-surface-variant font-label-md text-label-md hover:opacity-80 transition-opacity"
                    href="{{ route('frontend.home') }}">Beranda</a>
                <a class="text-on-surface-variant font-label-md text-label-md hover:opacity-80 transition-opacity"
                    href="{{ route('frontend.menu') }}">Menu</a>
                <a class="text-on-surface-variant font-label-md text-label-md hover:opacity-80 transition-opacity"
                    href="{{ route('frontend.reservation') }}">Reservasi</a>
            </nav>
        </div>
    </header>
    <main class="flex-grow flex items-center justify-center py-section-gap px-gutter">
        <div
            class="w-full max-w-[1120px] grid grid-cols-1 md:grid-cols-2 bg-surface-container-low rounded-xl overflow-hidden shadow-[0_20px_40px_rgba(53,64,36,0.04)] border border-secondary/10">
            <div class="hidden md:block relative h-full min-h-[600px]">
                <img alt="Saji Restaurant Food" class="absolute inset-0 w-full h-full object-cover"
                    src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop" />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent flex flex-col justify-end p-12">
                    <h2 class="font-headline-lg text-headline-lg text-white mb-4">Nikmati Cita Rasa Eksklusif</h2>
                    <p class="font-body-md text-body-md text-white/90">Bergabunglah dengan komunitas kuliner Saji dan
                        rasakan pengalaman reservasi yang tenang serta berkelas.</p>
                </div>
            </div>
            <div class="p-8 md:p-16 flex flex-col justify-center bg-surface">
                <div class="mb-10">
                    <h1 class="font-headline-lg text-headline-lg text-primary mb-2">Mulai Perjalanan Anda</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">Lengkapi data di bawah ini untuk
                        membuat akun coffith baru.</p>
                </div>
                @if (session('info'))
                    <div class="mb-6 rounded-lg bg-primary/10 border border-primary/20 px-4 py-3 font-body-md text-body-md text-primary">
                        {{ session('info') }}
                    </div>
                @endif
                <form class="space-y-5" action="{{ route('frontend.register.submit') }}" method="post">
                    @csrf
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest"
                            for="name">Nama Lengkap</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">person</span>
                            <input
                                class="w-full pl-8 pr-4 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-colors font-body-md text-body-md outline-none"
                                id="name" name="name" placeholder="Masukkan nama lengkap Anda" type="text" value="{{ old('name') }}" required />
                        </div>
                        @error('name')
                            <p class="font-label-sm text-label-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest"
                            for="email">Email</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">mail</span>
                            <input
                                class="w-full pl-8 pr-4 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-colors font-body-md text-body-md outline-none"
                                id="email" name="email" placeholder="contoh@email.com" type="email" value="{{ old('email') }}" required />
                        </div>
                        @error('email')
                            <p class="font-label-sm text-label-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest"
                            for="phone">Nomor Telepon</label>
                        <div class="relative flex items-center border-b border-outline-variant focus-within:border-primary transition-colors">
                            <span
                                class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">call</span>
                            <span class="pl-8 pr-1 py-3 font-body-md text-body-md text-on-surface-variant select-none whitespace-nowrap">+62</span>
                            <span class="text-outline-variant/60 select-none">|</span>
                            <input
                                class="w-full pl-2 pr-4 py-3 bg-transparent border-none focus:ring-0 font-body-md text-body-md outline-none"
                                id="phone" name="phone" placeholder="812 3456 7890" type="tel" value="{{ old('phone') }}" />
                        </div>
                        @error('phone')
                            <p class="font-label-sm text-label-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest"
                            for="password">Kata Sandi</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">lock</span>
                            <input
                                class="w-full pl-8 pr-4 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-colors font-body-md text-body-md outline-none"
                                id="password" name="password" placeholder="Minimal 8 karakter" type="password" required />
                        </div>
                        @error('password')
                            <p class="font-label-sm text-label-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <button
                        class="w-full py-4 bg-primary text-white font-label-md text-label-md rounded-lg hover:opacity-90 active:scale-[0.98] transition-all flex justify-center items-center gap-2"
                        type="submit">
                        Daftar
                    </button>
                </form>
                <div class="mt-10 pt-6 border-t border-secondary/10 flex flex-col items-center gap-3">
                    <p class="font-body-md text-body-md text-on-surface-variant">Sudah memiliki akun? <a
                            href="{{ route('login') }}"
                            class="text-primary font-bold hover:opacity-70 transition-opacity">Masuk</a></p>
                    <div class="flex items-center gap-3 w-full my-2">
                        <hr class="flex-1 border-secondary/10" />
                        <span
                            class="font-label-sm text-label-sm text-on-surface-variant/50 uppercase tracking-widest">Atau</span>
                        <hr class="flex-1 border-secondary/10" />
                    </div>
                    <a href="{{ route('auth.google.redirect') }}"
                        class="w-full py-3 border border-outline-variant rounded-lg hover:bg-surface-container transition-all flex justify-center items-center gap-2">
                        <svg aria-hidden="true" class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M21.6 12.23c0-.74-.07-1.45-.19-2.13H12v4.03h5.38a4.6 4.6 0 0 1-1.99 3.02v2.51h3.23c1.89-1.74 2.98-4.3 2.98-7.43z" />
                            <path fill="#34A853" d="M12 22c2.7 0 4.96-.89 6.62-2.34l-3.23-2.51c-.9.6-2.04.95-3.39.95-2.6 0-4.8-1.76-5.59-4.12H3.07v2.59A10 10 0 0 0 12 22z" />
                            <path fill="#FBBC05" d="M6.41 13.98A6.01 6.01 0 0 1 6.1 12c0-.69.11-1.35.31-1.98V7.43H3.07A10 10 0 0 0 2 12c0 1.61.39 3.14 1.07 4.57l3.34-2.59z" />
                            <path fill="#EA4335" d="M12 5.9c1.47 0 2.79.51 3.83 1.5l2.86-2.86C16.96 2.93 14.7 2 12 2a10 10 0 0 0-8.93 5.43l3.34 2.59C7.2 7.66 9.4 5.9 12 5.9z" />
                        </svg>
                        <span class="font-label-md text-label-md">Daftar dengan Google</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
    <footer class="w-full mt-section-gap bg-surface-container-low border-t border-secondary/50">
        <div
            class="flex flex-col md:flex-row justify-between items-center px-gutter py-base max-w-[1280px] mx-auto gap-4">
            <div class="font-label-sm text-label-sm text-secondary">© 2026 coffith Coffee &amp; Kitchen. All rights
                reserved.</div>
            <div class="flex gap-6">
                <a class="font-label-sm text-label-sm text-on-secondary-container hover:text-primary transition-colors"
                    href="#">Privacy Policy</a>
                <a class="font-label-sm text-label-sm text-on-secondary-container hover:text-primary transition-colors"
                    href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>

</html>
