<?php
namespace Groupeat\Auth;

use Dingo\Api\Auth\Auth as DingoAuth;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Auth\Listeners\SendWelcomeMail;
use Groupeat\Auth\Values\TokenDurationInMinutes;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        TokenDurationInMinutes::class => 'jwt.ttl',
    ];

    protected $listeners = [
        SendWelcomeMail::class => UserHasRegistered::class,
    ];

    protected function registerPackage()
    {
        $this->app->singleton(Auth::class, function ($app) {
            return new Auth($app['tymon.jwt.auth'], $app['auth.driver'], $app['api.auth']);
        });
    }

    protected function bootPackage()
    {
        $this->app[DingoAuth::class]->extend('custom', function ($app) {
            return $app[Auth::class];
        });

        $this->addUserInLogContext();
    }

    private function addUserInLogContext()
    {
        $this->app[LoggerInterface::class]->getMonolog()->pushProcessor(function ($record) {
            $auth = $this->app[Auth::class];

            if ($auth->check()) {
                $user = $auth->user();
                $record['context'][$auth->shortTypeOf($user).'Id'] = $user->id;
            }

            $record['context']['IP'] = $this->app[Request::class]->ip();

            return $record;
        });
    }
}
