<?php namespace Groupeat\Support;

use Groupeat\Support\Providers\WorkbenchPackageProvider;
use Groupeat\Support\Values\AvailableLocales;
use Groupeat\Support\Values\Environment;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::HELPERS, self::ROUTES];
    protected $console = ['DbInstall'];

    public function register()
    {
        $this->bindValueFromConfig(
            AvailableLocales::class,
            'app.available_locales'
        );

        $this->bindValue(
            Environment::class,
            $this->app->environment()
        );

        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
