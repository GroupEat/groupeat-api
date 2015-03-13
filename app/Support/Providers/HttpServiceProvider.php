<?php
namespace Groupeat\Support\Providers;

use Groupeat\Support\Http\Output;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use League\Fractal\Manager;

class HttpServiceProvider extends IlluminateServiceProvider
{
    /**
    * Bootstrap the application services.
    *
    * @return void
    */
    public function boot()
    {
        //
    }

    /**
    * Register the application services.
    *
    * @return void
    */
    public function register()
    {
        $this->app->instance('League\Fractal\Manager', new Manager);

        $this->app->singleton('Groupeat\Http\Responses\Output', function ($app) {
            return new Output($app['League\Fractal\Manager']);
        });
    }
}
