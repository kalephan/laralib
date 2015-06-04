<?php namespace Kalephan\LKS;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class LKSServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
	    // Publishes
		$this->publishes([
		  __DIR__ . '/../config/lks.php' => config_path('lks.php'),
		], 'config');
		$this->publishes([
		  __DIR__ . '/../database/migrations/' => base_path('/database/migrations'),
		], 'migrations');
		
		// Set view paths
		Config::set('view.paths', lks_view_paths());
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
		$this->app->singleton('Output', function(){ return new Output; });
		$this->app->singleton('Asset', function(){ return new Asset; });
		$this->app->bind('Form', function(){ return new Form; });

		// ALIAS
		$this->app->booting(function()
        {
            $loader = AliasLoader::getInstance();

	        $loader->alias('HTML', 'Illuminate\Html\HtmlFacade');
	        $loader->alias('FormLaravel', 'Illuminate\Html\FormFacade');

            $loader->alias('Output', 'Kalephan\LKS\Facades\Output');
            $loader->alias('Asset', 'Kalephan\LKS\Facades\Asset');
	        $loader->alias('Form', 'Kalephan\LKS\Facades\Form');
        });
	}
}
