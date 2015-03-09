<?php
namespace Groupeat\Auth;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\ActivateUser;
use Groupeat\Auth\Services\GenerateAuthToken;
use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Auth\Services\SendActivationLink;
use Groupeat\Auth\Services\SendPasswordResetLink;
use Groupeat\Auth\Services\ResetPassword;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::HELPERS, self::FILTERS, self::ROUTES];

    public function register()
    {
        $this->app->bind('ActivateUserService', function () {
            return new ActivateUser();
        });

        $this->app->bind('GenerateAuthTokenService', function ($app) {
            return new GenerateAuthToken(
                $app['tymon.jwt.auth'],
                $app['config']->get('jwt-auth::ttl')
            );
        });

        $this->app->bind('RegisterUserService', function ($app) {
            return new RegisterUser(
                $app['validator'],
                $app['events'],
                $app['GenerateAuthTokenService'],
                $app['groupeat.locale']
            );
        });

        $this->app->bind('SendActivationLinkService', function ($app) {
            return new SendActivationLink($app['SendMailService'], $app['url']);
        });

        $this->app->bind('SendPasswordResetLinkService', function ($app) {
            return new SendPasswordResetLink($app['auth.reminder'], $app['groupeat.locale'], $app['url']);
        });

        $this->app->bind('ResetPasswordService', function ($app) {
            return new ResetPassword(
                $app['GenerateAuthTokenService'],
                $app['auth.reminder'],
                $app['translator']
            );
        });

        $this->app->bindShared('groupeat.auth', function ($app) {
            $auth = new Auth(new UserCredentials, $app['tymon.jwt.provider'], $app['auth'], $app['request']);

            return $auth->setIdentifier($app['config']->get('jwt::identifier'));
        });
    }

    public function boot()
    {
        parent::boot();

        $this->app['events']->listen('userHasRegistered', 'SendActivationLinkService@call');
    }
}
