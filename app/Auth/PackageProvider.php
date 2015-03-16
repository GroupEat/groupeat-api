<?php
namespace Groupeat\Auth;

use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Auth\Handlers\Events\SendActivationLink;
use Groupeat\Auth\Values\TokenDurationInMinutes;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            TokenDurationInMinutes::class,
            'jwt.ttl'
        );

        $this->app->singleton(Auth::class, function ($app) {
            return new Auth($app['tymon.jwt.auth'], $app['auth.driver']);
        });
    }

    protected function bootPackage()
    {
        $this->listen(UserHasRegistered::class, SendActivationLink::class);
    }
}
