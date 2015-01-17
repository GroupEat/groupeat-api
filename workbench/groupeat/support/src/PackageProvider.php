<?php namespace Groupeat\Support;

use Groupeat\Support\Services\Locale;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::HELPERS, self::FILTERS, self::ROUTES];
    protected $console = ['DbInstall'];


    public function register()
    {
        $this->app->bindShared('groupeat.locale', function($app)
        {
            return new Locale(
                $app['router'],
                $app['translator'],
                $app['config']->get('app.available_frontend_locales')
            );
        });

        $this->app['events']->listen('groupeat.auth.login', function($userCredentials)
        {
            $this->app['groupeat.locale']->set($userCredentials->locale);
        });

        $this->app['router']->before(function($request)
        {
            $this->app['groupeat.locale']->detectAndSetIfNeeded($request);
        });
    }

}
