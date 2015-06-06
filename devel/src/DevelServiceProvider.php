<?php

namespace Kalephan\Devel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class DevelServiceProvider extends ServiceProvider
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
            __DIR__ . '/../config/devel.php' => config_path('devel.php')
        ], 'config');
        
        if ($this->app->environment('local') && config('devel.enabled', true)) {
            // Register Barryvdh\Debugbar
            $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        }
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // CONFIG
        $this->mergeConfigFrom(__DIR__ . '/../config/devel.php', 'devel');
    }
}
