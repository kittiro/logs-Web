<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Services\FileUserProvider;

class FileAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Auth::provider('file', function ($app, array $config) {
            return new FileUserProvider();
        });
    }
}
