<?php
namespace Groupeat\Auth;

use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Auth\Handlers\Events\SendActivationLink;
use Groupeat\Auth\Values\TokenDurationInMinutes;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

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

        $this->addUserInLogContext();
    }

    private function addUserInLogContext()
    {
        $this->app[LoggerInterface::class]->pushProcessor(function ($record) {
            $auth = $this->app[Auth::class];

            if ($auth->check()) {
                $user = $auth->user();
                $record['context'][$auth->shortTypeOf($user)] = $user->id;
            }

            $record['context']['IP'] = $this->app[Request::class]->ip();

            return $record;
        });
    }
}
