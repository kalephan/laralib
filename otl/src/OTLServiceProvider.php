<?php

namespace Kalephan\OTL;

use Illuminate\Support\ServiceProvider;

class OTLServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishes
        $this->publishes([
            __DIR__ . '/../config/otl.php' => config_path('otl.php')
        ], 'config');
        $this->publishes([
            __DIR__ . '/../database/migrations/' => base_path('/database/migrations')
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // CONFIG
        $this->mergeConfigFrom(__DIR__ . '/../config/otl.php', 'lks');
    }
}
