<?php namespace Groupeat\Auth;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\ActivateUser;
use Groupeat\Auth\Services\DeleteUser;
use Groupeat\Auth\Services\GenerateTokenForUser;
use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Auth\Services\SendResetPasswordLink;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::FILTERS, self::ROUTES];


    public function register()
    {
        $this->app->bind('ActivateUserService', function()
        {
            return new ActivateUser;
        });

        $this->app->bind('DeleteUserService', function($app)
        {
            return new DeleteUser($app['groupeat.auth']);
        });

        $this->app->bind('GenerateTokenForUserService', function($app)
        {
            return new GenerateTokenForUser($app['tymon.jwt.auth'], $app['groupeat.auth']);
        });

        $this->app->bind('RegisterUserService', function($app)
        {
            return new RegisterUser(
                $app['mailer'],
                $app['validator'],
                $app['url'],
                $app['GenerateTokenForUserService']
            );
        });

        $this->app->bind('SendResetPasswordLinkService', function($app)
        {
            return new SendResetPasswordLink($app['mailer'], $app['url']);
        });

        $this->app->bindShared('groupeat.auth', function($app)
        {
            $auth = new Auth(new UserCredentials, $app['tymon.jwt.provider'], $app['auth'], $app['request']);

            return $auth->setIdentifier($app['config']->get('jwt::identifier'));
        });
    }

}
