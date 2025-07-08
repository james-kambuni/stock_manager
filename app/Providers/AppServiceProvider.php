<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
   // app/Providers/AppServiceProvider.php

public function boot()
{
    if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
    }
}

    
}
