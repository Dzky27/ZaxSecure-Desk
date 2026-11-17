<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use App\Http\Middleware\Authenticate as CustomAuthenticate;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthenticatesRequests::class, function ($app) {
            return new CustomAuthenticate($app->make(\Illuminate\Contracts\Auth\Factory::class));
        });
    }
}
