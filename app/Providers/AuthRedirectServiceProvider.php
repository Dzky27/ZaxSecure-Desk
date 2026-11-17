<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;

class AuthRedirectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(RouteRegistrar $router): void
    {
        Authenticate::redirectUsing(function ($request) {
            return route('auth.login');
        });
    }
}
