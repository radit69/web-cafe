<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show staff login page.
     */
    public function selectRole()
    {
        return view('backend.auth.login');
    }

    /**
     * Redirect legacy role login URLs to the single staff login page.
     */
    public function showLoginForm(string $role)
    {
        return redirect()->route('select.role');
    }

    /**
     * Process staff login and redirect by the user's role.
     */
    public function login(Request $request, ?string $role = null)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $username = $request->input('username');
        $user = User::whereIn('role', ['admin', 'kasir'])
            ->where('is_active', true)
            ->where(function ($q) use ($username) {
                $q->where('name', $username)->orWhere('email', $username);
            })
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['password' => 'Username/email atau password staff tidak valid.']);
        }

        Auth::login($user);

        $user->update(['last_login_at' => now()]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'kasir') {
            return redirect()->route('kasir.menu');
        }

        Auth::logout();

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => 'Akun ini bukan akun staff admin atau kasir.']);
    }

    public function customerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])
            ->where('role', 'pelanggan')
            ->first();

        if (! $user) {
            return redirect()->route('frontend.register')
                ->withInput($request->only('email'))
                ->with('info', 'Email belum terdaftar. Silakan daftar terlebih dahulu.');
        }

        if (! $user->is_active || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau kata sandi customer tidak valid.']);
        }

        Auth::login($user, true);

        $user->update(['last_login_at' => now()]);

        return redirect()->route('frontend.reservation');
    }

    public function customerRegister(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => !empty($data['phone']) ? '+62' . ltrim($data['phone']) : null,
            'password' => Hash::make($data['password']),
            'role' => 'pelanggan',
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        Auth::login($user, true);

        return redirect()->route('frontend.reservation');
    }

    public function profileEdit()
    {
        return view('frontend.profile');
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        if ($request->input('type') === 'username') {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ], [
                'name.required' => 'Nama tidak boleh kosong.',
                'name.max' => 'Nama maksimal 255 karakter.',
            ]);

            $user->update(['name' => $request->input('name')]);

            return back()->with('success', 'Nama berhasil diperbarui.');
        }

        if ($request->input('type') === 'password') {
            $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'current_password.required' => 'Kata sandi saat ini wajib diisi.',
                'new_password.required' => 'Kata sandi baru wajib diisi.',
                'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
                'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            ]);

            if (! Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
            }

            $user->update(['password' => Hash::make($request->input('new_password'))]);

            return back()->with('success', 'Kata sandi berhasil diubah.');
        }

        return back();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home');
    }

    public function redirectToGoogle()
    {
        $state = Str::random(40);

        session(['google_oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Login Google dibatalkan atau gagal.']);
        }

        if (! $request->filled('code') || $request->input('state') !== session()->pull('google_oauth_state')) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Sesi login Google tidak valid. Silakan coba lagi.']);
        }

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'grant_type' => 'authorization_code',
            'code' => $request->input('code'),
        ]);

        if (! $tokenResponse->successful() || ! $tokenResponse->json('access_token')) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Gagal mengambil token Google. Silakan coba lagi.']);
        }

        $googleUserResponse = Http::withToken($tokenResponse->json('access_token'))
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if (! $googleUserResponse->successful() || ! $googleUserResponse->json('email')) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Gagal mengambil profil Google. Silakan coba lagi.']);
        }

        $googleUser = $googleUserResponse->json();

        $user = User::where('google_id', $googleUser['sub'])
            ->orWhere('email', $googleUser['email'])
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser['sub'],
                'avatar' => $googleUser['picture'] ?? $user->avatar,
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser['name'] ?? Str::before($googleUser['email'], '@'),
                'email' => $googleUser['email'],
                'google_id' => $googleUser['sub'],
                'avatar' => $googleUser['picture'] ?? null,
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'pelanggan',
                'is_active' => true,
                'last_login_at' => now(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('frontend.reservation');
    }
}


