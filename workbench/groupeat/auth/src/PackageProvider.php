<?php namespace Groupeat\Auth;

use Groupeat\Auth\Services\DeleteUser;
use Groupeat\Auth\Services\GenerateTokenForUser;
use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = ['routes'];


    protected function registerServices()
    {
        $this->app->bind('ActivateUserService', function()
        {
            return new ActivateUser;
        });

        $this->app->bind('DeleteUserService', function($app)
        {
            return new DeleteUser($app['auth']);
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

}
