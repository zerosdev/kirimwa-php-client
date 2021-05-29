<?php

namespace ZerosDev\KirimWA\Laravel;

use ZerosDev\KirimWA\Client;
use ZerosDev\KirimWA\Config;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
	public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
        	$configFile = config_path('kirimwa.php');
        	if( file_exists($configFile) && is_readable($configFile) ) {
        		Config::load($configFile);
        	}
            return new Client();
        });
    }

    public function boot()
    {
        $this->publishes([
	        dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'config.php' => config_path('kirimwa.php'),
	    ]);
    }

    public function provides()
    {
        return [Client::class];
    }
}