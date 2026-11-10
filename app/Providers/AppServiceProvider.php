<?php

namespace App\Providers;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {}
    
    public function boot(): void
    {Password::defaults(function () {
            return Password::min(8)
             ->letters()
             ->mixedCase()
             ->numbers()
             ->symbols();
        });
    }
}
