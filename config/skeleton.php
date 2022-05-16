<?php

use Benhdev\Skeleton\Skeleton;

return [

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
        'custom' => [
            'driver' => \App\Guards\ExampleGuard::class,
            'provider' => 'users'
        ],
    ],

];
