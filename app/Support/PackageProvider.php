<?php namespace Groupeat\Support;

use Groupeat\Support\Services\Locale;
use Groupeat\Support\Providers\WorkbenchPackageProvider;
use Groupeat\Support\Services\SendMail;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::HELPERS, self::FILTERS, self::ROUTES];
    protected $console = ['DbInstall'];

    public function register()
    {
        $this->app->bindShared('groupeat.locale', function ($app) {
            return new Locale(
                $app['router'],
                $app['translator'],
                $app['config']->get('app.available_mailing_locales')
            );
        });

        $this->app->bind('SendMailService', function ($app) {
            return new SendMail($app['mailer'], $app['groupeat.locale']);
        });

        if ($this->app->isLocal()) {
            $this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
        }
    }
}
