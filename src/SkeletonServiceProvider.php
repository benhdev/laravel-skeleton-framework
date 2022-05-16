<?php

namespace Skeleton;

use Skeleton\Console\Commands\GuardMakeCommand;
use Skeleton\Console\Commands\HelperMakeCommand;
use Skeleton\Console\Commands\TraitMakeCommand;

use Skeleton\Exceptions\NotFoundException;

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
        if ($this->app->runningInConsole()) {
            $this->commands([
                GuardMakeCommand::class,
                HelperMakeCommand::class,
                TraitMakeCommand::class
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/skeleton.php' => config_path('skeleton.php')
        ], 'skeleton');

        foreach ($this->guards() as $name => $config) {
            if (!class_exists($driver = $config['driver'])) {
                throw new NotFoundException("Class $driver not found");
            }

            $reflectionDriver = new ReflectionClass($config['driver']);
            $driver = Str::kebab($reflectionDriver->getShortName());

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

        $this->app->auth->shouldUse(config('skeleton.defaults.guard', null));
    }

    protected function guards()
    {
        return config('skeleton.guards');
    }
}
