<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;

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
}