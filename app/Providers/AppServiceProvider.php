<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\Service;

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
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Share services globally (tenant-based)
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with(
                    'services',
                    Service::where('tenant_id', auth()->user()->tenant_id)->get()
                );
            }
        });
    }
}