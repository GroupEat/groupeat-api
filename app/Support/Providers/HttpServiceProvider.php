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
        $this->app->instance(Manager::class, new Manager);
    }
}
