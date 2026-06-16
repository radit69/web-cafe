@extends('frontend.layout')

@section('title', 'Edit Profil | cOffith Coffee & Kitchen')

@section('content')
<main class="flex-grow flex items-center justify-center py-16 px-container-margin-mobile md:px-container-margin-desktop">
    <div class="w-full max-w-[560px]">

        {{-- Header --}}
        <div class="mb-10">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 font-label-md text-label-md text-secondary hover:text-primary transition-colors mb-6">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Kembali
            </a>
            <h1 class="font-headline-lg text-headline-lg text-primary mb-2">Edit Profil</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Perbarui nama pengguna atau kata sandi akun Anda.</p>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="rounded-xl bg-primary-container/30 border border-primary/20 px-5 py-4 mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[22px]">check_circle</span>
                <p class="font-body-md text-body-md text-primary">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Profile Card --}}
        <div class="bg-surface-container-low rounded-2xl border border-secondary/10 shadow-[0_8px_30px_rgba(53,64,36,0.04)] overflow-hidden">

            {{-- User Info Header --}}
            <div class="bg-gradient-to-r from-primary to-primary-container px-8 py-6 flex items-center gap-5">
                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-headline-md text-headline-md ring-2 ring-white/30">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-headline-md text-[20px] text-white font-semibold">{{ auth()->user()->name }}</h2>
                    <p class="font-label-md text-label-md text-white/70">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="p-8 space-y-8">

                {{-- Form: Ganti Username --}}
                <form method="POST" action="{{ route('frontend.profile.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="type" value="username">

                    <div class="flex items-center gap-3 mb-2">
                        <span class="material-symbols-outlined text-primary text-[22px]">person</span>
                        <h3 class="font-label-md text-label-md text-primary uppercase tracking-widest">Ganti Nama</h3>
                    </div>

                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="name">Nama Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">badge</span>
                            <input
                                class="w-full pl-8 pr-4 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-colors font-body-md text-body-md outline-none"
                                id="name" name="name" type="text" placeholder="Masukkan nama baru"
                                value="{{ old('name', auth()->user()->name) }}" required />
                        </div>
                        @error('name')
                            <p class="font-label-sm text-label-sm text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-primary text-white font-label-md text-label-md rounded-xl hover:opacity-90 active:scale-[0.98] transition-all flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Nama
                    </button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center gap-4">
                    <hr class="flex-1 border-secondary/10" />
                    <span class="font-label-sm text-label-sm text-on-surface-variant/40 uppercase tracking-widest">atau</span>
                    <hr class="flex-1 border-secondary/10" />
                </div>

                {{-- Form: Ganti Password --}}
                <form method="POST" action="{{ route('frontend.profile.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="type" value="password">

                    <div class="flex items-center gap-3 mb-2">
                        <span class="material-symbols-outlined text-primary text-[22px]">lock</span>
                        <h3 class="font-label-md text-label-md text-primary uppercase tracking-widest">Ganti Kata Sandi</h3>
                    </div>

                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="current_password">Kata Sandi Saat Ini</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">lock_open</span>
                            <input
                                class="w-full pl-8 pr-12 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-colors font-body-md text-body-md outline-none"
                                id="current_password" name="current_password" type="password" placeholder="Masukkan kata sandi saat ini" required />
                            <button type="button" onclick="togglePassword('current_password', this)" class="absolute right-0 top-1/2 -translate-y-1/2 text-outline-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="font-label-sm text-label-sm text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="new_password">Kata Sandi Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">lock</span>
                            <input
                                class="w-full pl-8 pr-12 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-colors font-body-md text-body-md outline-none"
                                id="new_password" name="new_password" type="password" placeholder="Minimal 8 karakter" required />
                            <button type="button" onclick="togglePassword('new_password', this)" class="absolute right-0 top-1/2 -translate-y-1/2 text-outline-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="font-label-sm text-label-sm text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="font-label-sm text-label-sm text-secondary uppercase tracking-widest" for="new_password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-0 top-1/2 -translate-y-1/2 text-outline-variant">lock</span>
                            <input
                                class="w-full pl-8 pr-12 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-colors font-body-md text-body-md outline-none"
                                id="new_password_confirmation" name="new_password_confirmation" type="password" placeholder="Ketik ulang kata sandi baru" required />
                            <button type="button" onclick="togglePassword('new_password_confirmation', this)" class="absolute right-0 top-1/2 -translate-y-1/2 text-outline-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-primary text-white font-label-md text-label-md rounded-xl hover:opacity-90 active:scale-[0.98] transition-all flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                        Ubah Kata Sandi
                    </button>
                </form>

            </div>
        </div>
    </div>
</main>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
@endsection
