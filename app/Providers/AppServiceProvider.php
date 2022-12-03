<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        'AOSAPI' => \App\Services\AOSService::class,
        'Cafe24ApiService' => \App\Services\Cafe24ApiService::class,
        'Cafe24Service' => \App\Services\Cafe24Service::class,
        \App\Contracts\ApiResponseInterface::class => \App\Http\Response\ApiResponse::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();
    }
}
