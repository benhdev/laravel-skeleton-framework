<?php

namespace Laravel\Sanctum;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class SkeletonServiceProvider extends ServiceProvider
{
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
        Log::debug('test');
    }
}
