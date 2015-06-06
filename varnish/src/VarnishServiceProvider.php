<?php

namespace Kalephan\Varnish;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class VarnishServiceProvider extends ServiceProvider
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
            __DIR__ . '/../config/varnish.php' => config_path('varnish.php')
        ], 'config');
        
        if (Request::segment(1) == config('varnish.first_segment', 'varnish')) {
            Config::set('devel.enabled', false);
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
        $this->mergeConfigFrom(__DIR__ . '/../config/varnish.php', 'varnish');
    }
}
