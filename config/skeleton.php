<?php

use Benhdev\Skeleton\Skeleton;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" for your application.
    |
    | You may change these defaults as required, however you must set
    | the default guard in config/auth.php to 'skeleton' for this to work
    |
    |
    */

    'defaults' => [
        'guard' => 'skeleton',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Custom authentication guard mappings for the application
    |
    | To create a new guard you can run
    | php artisan make:guard ExampleGuard
    |
    | You can then use this guard in route definitions
    | using the guard name as below
    |
    | Route::middleware('auth:custom')
    |
    */

    'guards' => [

        'skeleton' => [
            'driver' => \Skeleton\Guards\SkeletonGuard::class,
            'provider' => 'users'
        ],

        // 'custom' => [
        //     'driver' => \App\Guards\ExampleGuard::class,
        //     'provider' => 'users'
        // ],

    ],

];
