<?php

namespace Kalephan\LKS;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class LKSServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Lumen users need to copy the config file over to /config themselves
        //and it needs to be pulled in with $this->app->configure().
        if (str_contains($this->app->version(), 'Lumen')) {
            $this->app->configure('lks');
        }
        //Laravel users can run artisan config:publish and config will be
        //automatically read in with directory scanning.
        else {
            $this->publishes([
                __DIR__ . '/../config/lks.php' => config_path('lks.php')
            ], 'config');
        }

        // Set view paths
        Config::set('view.paths', lks_view_paths());

        require_once __DIR__ . '/macro.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // CONFIG
        $this->mergeConfigFrom(__DIR__ . '/../config/lks.php', 'lks');

        // BIND
        $this->app->singleton('Output', function () {
            return new Output();
        });
        $this->app->singleton('Asset', function () {
            return new Asset();
        });
        $this->app->bind('Form', function () {
            return new Form();
        });

        // Register Illuminate\Html
        $this->app->register('Illuminate\Html\HtmlServiceProvider');

        // ALIAS
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();

            $loader->alias('HTML', 'Illuminate\Html\HtmlFacade');
            $loader->alias('FormLaravel', 'Illuminate\Html\FormFacade');

            $loader->alias('Output', 'Kalephan\LKS\Facades\Output');
            $loader->alias('Asset', 'Kalephan\LKS\Facades\Asset');
            $loader->alias('Form', 'Kalephan\LKS\Facades\Form');
        });
    }
}
