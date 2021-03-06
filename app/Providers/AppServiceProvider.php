<?php

namespace App\Providers;

use App\Channel;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('*', function($view) {
            // caching channel data
            $channels = \Cache::rememberForever('channel', function() {
                return Channel::all();
            });

            $view->with('channels', $channels);      
        });

        // Runs before DatabaseMigration trait in testing class, need to revert to View::composer
        // \View::share('channels', Channel::all());

        \Validator::extend('spamfree', 'App\Rules\SpamFree@passes');

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
