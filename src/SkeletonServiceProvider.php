<?php

namespace Benhdev\Skeleton;

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
        $this->mergeConfigFrom(
            __DIR__.'/../config/skeleton.php', 'skeleton'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/skeleton.php' => config_path('skeleton.php')
        ]);
    }
}
