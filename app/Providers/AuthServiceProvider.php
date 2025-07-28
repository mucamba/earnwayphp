<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        // Gate 1: Only Merchants can view merchant pages
        Gate::define('merchant', function (User $user) {
            return $user->role === UserRole::MERCHANT;
        });

        // Gate 2: Only standard users can view user pages
        Gate::define('user', function (User $user) {
            return $user->role === UserRole::USER;
        });
    }
}
