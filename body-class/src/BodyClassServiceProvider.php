<?php

namespace Kalephan\BodyClass;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class BodyClassServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // BIND
        $this->app->singleton('BodyClass', function () {
            return new BodyClass();
        });
        
        // ALIAS
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            
            $loader->alias('BodyClass', 'Kalephan\BodyClass\Facades\BodyClass');
        });
    }
}
