<?php namespace Groupeat\Auth;

use Groupeat\Auth\Services\ActivateUser;
use Groupeat\Auth\Services\DeleteUser;
use Groupeat\Auth\Services\GenerateTokenForUser;
use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];


    public function register()
    {
        parent::register();

        $this->registerAuthDriver();
    }

    protected function registerServices()
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
            return new GenerateTokenForUser($app['tymon.jwt.auth']);
        });

        $this->app->bind('RegisterUserService', function($app)
        {
            return new RegisterUser($app['mailer'], $app['validator'], $app['GenerateTokenForUserService']);
        });
    }

    private function registerAuthDriver()
    {
        $this->app->bindShared('groupeat.auth', function($app)
        {
            $userClass = $app['config']->get('jwt::user');

            $auth = new Auth(new $userClass, $app['tymon.jwt.provider'], $app['auth'], $app['request']);

            return $auth->setIdentifier($app['config']->get('jwt::identifier'));
        });
    }

}
