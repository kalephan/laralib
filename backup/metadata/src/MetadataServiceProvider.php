<?php

namespace Kalephan\Metadata;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MetadataServiceProvider extends ServiceProvider
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
        // BIND
        $this->app->singleton('Metadata', function () {
            return new Metadata();
        });

        // ALIAS
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();

            $loader->alias('Metadata', 'Kalephan\Metadata\Facades\Metadata');
        });
    }
}
