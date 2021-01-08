<?php
namespace Esterisk\Backend;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BackendServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
	    	// views
        $this->loadViewsFrom(__DIR__.'/views/backend', 'backend');
        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/esterisk/backend'),
        ]);
        
        // config
        $this->publishes([
			__DIR__.'/config/backend.php' => config_path('backend.php'),
	    ]);
        
        // routes
		$this->registerRoutes();
	}

	protected function registerRoutes()
	{
//		Route::group($this->routeConfiguration(), function () {
			$this->loadRoutesFrom(__DIR__.'/routes/web.php');
//		});
	}

	protected function routeConfiguration()
	{
		foreach (config('backend.sets') as $manager => $configs) {
		
			return [
				$configs['urlPosition'] => $manager,
				'middleware' => ( isset($configs['middleware']) ? $configs['middleware'] : config('backend.middleware', [ 'web' ])),
			];

		}
	}
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
