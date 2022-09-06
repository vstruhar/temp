<?php

namespace App\Providers;

use Hubipe\FinStat\FinStat;
use Hubipe\FinStat\Request\GuzzleRequestClient;
use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Inertia::share('appUrl', config('app.url'));

        $this->app->singleton(FinStat::class, function ($app) {
            $requestClient = new GuzzleRequestClient();

            return new FinStat(
                $requestClient,
                config('services.finstat.api_key'),
                config('services.finstat.private_key')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
