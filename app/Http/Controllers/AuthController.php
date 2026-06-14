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
     * Show user type selection page.
     */
    public function selectRole()
    {
        return view('backend.auth.select_role');
    }

    /**
     * Show login form for given role.
     */
    public function showLoginForm(string $role)
    {
        $role = strtolower($role);

        if (! in_array($role, ['admin', 'kasir'])) {
            abort(404);
        }

        return view('backend.auth.login', [
            'role' => $role,
        ]);
    }

    /**
     * Process login with simple hardcoded passwords per role.
     */
    public function login(Request $request, string $role)
    {
        $role = strtolower($role);

        if (! in_array($role, ['admin', 'kasir'])) {
            abort(404);
        }

        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Cek apakah ada user aktif untuk role tersebut
        $exists = User::where('role', $role)->where('is_active', true)->exists();
        if (! $exists) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Belum ada user aktif untuk role '.ucfirst($role).'. Tambahkan user di Admin terlebih dahulu.']);
        }

        $username = $request->input('username');
        $user = User::where('role', $role)
            ->where('is_active', true)
            ->where(function($q) use ($username){
                $q->where('name', $username)->orWhere('email', $username);
            })->first();
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['password' => 'Kredensial tidak valid untuk role '.ucfirst($role).'.']);
        }

        // Jika admin, arahkan ke dashboard admin.
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Jika kasir, arahkan ke menu kasir.
        if ($role === 'kasir') {
            return redirect()->route('kasir.menu');
        }
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
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'pelanggan',
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        Auth::login($user, true);

        return redirect()->route('frontend.reservation');
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


