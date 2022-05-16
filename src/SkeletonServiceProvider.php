<?php

namespace Benhdev\Skeleton;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Log;

use ReflectionClass;

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

        foreach ($this->guards() as $name => $config) {
            if (!class_exists($config['driver'])) {
                continue;
            }

            $reflectionDriver = new ReflectionClass($config['driver']);
            $driver = Str::slug($reflectionDriver->getShortName());

            Log::debug($driver);

            config([
                "auth.guards.{$name}" => array_merge([
                    'driver' => $driver,
                    'provider' => $config['provider'] ?? null,
                ], config("auth.guards.{$name}", [])),
            ]);

            $this->app->auth->extend($driver, function ($app, $name, array $config) use ($reflectionDriver) {
                return $reflectionDriver->newInstance(
                    $app->auth->createUserProvider($config['provider'] ?? null), $app->request
                );
            });
        }
    }

    protected function guards()
    {
        return config('skeleton.guards');
    }
}
