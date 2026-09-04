<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Vercel's edge terminates TLS and forwards to the function over plain
        // HTTP, so without this, url()/asset() generate http:// links.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();
    }
}
