<?php

namespace App\Providers;

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
        view()->composer('layouts.sidebar', function ($view) {
            $city_mode = collect(getSettings('city_mode'))->isNotEmpty() ? getSettings('city_mode')['city_mode'] : 1 ;
            $view->with('city_mode', $city_mode);
        });
    }
}
