<?php
namespace Groupeat\Auth;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Auth\Services\SendActivationLink;
use Groupeat\Auth\Values\TokenDurationInMinutes;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::ROUTES];

    public function register()
    {
        parent::register();

        $this->bindValueFromConfig(
            TokenDurationInMinutes::class,
            'jwt.ttl'
        );

        $this->app->singleton(Auth::class, function ($app) {
            return new Auth($app['tymon.jwt.auth'], $app['auth.driver']);
        });

//        $this->app->singleton(Auth::class, function ($app) {
//            $auth = new Auth(
//                $app['tymon.jwt.manager'],
//                new UserCredentials,
//                $app['tymon.jwt.provider.auth'],
//                $app['request']
//            );
//
//            return $auth->setIdentifier($app['config']->get('jwt.identifier'));
//        });
    }

    public function boot()
    {
        parent::boot();

        $this->listen(UserHasRegistered::class, SendActivationLink::class);
    }
}
