<?php namespace Groupeat\Auth;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\ActivateUser;
use Groupeat\Auth\Services\GenerateAuthToken;
use Groupeat\Auth\Services\GenerateTokenForUser;
use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Auth\Services\SendResetPasswordLink;
use Groupeat\Auth\Services\ResetPassword;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::FILTERS, self::ROUTES];


    public function register()
    {
        $this->app->bind('ActivateUserService', function()
        {
            return new ActivateUser;
        });

        $this->app->bind('GenerateAuthTokenService', function($app)
        {
            return new GenerateAuthToken($app['tymon.jwt.auth']);
        });

        $this->app->bind('RegisterUserService', function($app)
        {
            return new RegisterUser(
                $app['mailer'],
                $app['validator'],
                $app['url'],
                $app['GenerateAuthTokenService'],
                $app['groupeat.locale']
            );
        });

        $this->app->bind('SendResetPasswordLinkService', function($app)
        {
            return new SendResetPasswordLink($app['auth.reminder'], $app['groupeat.locale']);
        });

        $this->app->bind('ResetPasswordService', function($app)
        {
            return new ResetPassword($app['GenerateAuthTokenService'], $app['auth.reminder']);
        });

        $this->app->bindShared('groupeat.auth', function($app)
        {
            $auth = new Auth(new UserCredentials, $app['tymon.jwt.provider'], $app['auth'], $app['request']);

            return $auth->setIdentifier($app['config']->get('jwt::identifier'));
        });
    }

}
