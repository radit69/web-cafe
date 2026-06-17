<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->shouldForceHttps()) {
            URL::forceScheme('https');

            if ($host = $this->httpsHost()) {
                URL::forceRootUrl('https://' . $host);
            }
        }

        try {
            if (! User::where('role', 'admin')->exists()) {
                User::create([
                    'name'      => 'admin',
                    'email'     => 'admin@gmailp.com',
                    'password'  => bcrypt('admin123'),
                    'role'      => 'admin',
                    'is_active' => true,
                ]);
            }
        } catch (\Throwable) {
            // Skip when database is not available (e.g., during build)
        }
    }

    private function shouldForceHttps(): bool
    {
        $appUrl = (string) config('app.url');
        $host = request()->getHost();
        $forwardedProto = request()->header('x-forwarded-proto');

        return $this->app->environment('production')
            || str_starts_with($appUrl, 'https://')
            || str_contains($host, 'up.railway.app')
            || $forwardedProto === 'https'
            || env('RAILWAY_ENVIRONMENT') !== null
            || env('RAILWAY_PUBLIC_DOMAIN') !== null
            || env('RAILWAY_STATIC_URL') !== null;
    }

    private function httpsHost(): ?string
    {
        $host = request()->getHost();

        if ($host && $host !== 'localhost' && $host !== '127.0.0.1') {
            return $host;
        }

        $railwayDomain = env('RAILWAY_PUBLIC_DOMAIN');

        if ($railwayDomain) {
            return preg_replace('#^https?://#', '', $railwayDomain);
        }

        $appUrlHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        return $appUrlHost ?: null;
    }
}
