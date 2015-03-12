<?php
namespace Groupeat\Providers;

use Groupeat\Http\Responses\Output;
use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
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
