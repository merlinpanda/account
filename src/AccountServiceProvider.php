<?php

namespace Merlinpanda\Account;

use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'account');
        $this->loadViewsFrom(__DIR__ . '/../views', 'account');
    }
}
