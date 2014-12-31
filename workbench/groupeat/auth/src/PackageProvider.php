<?php namespace Groupeat\Auth;

use Groupeat\Auth\Services\DeleteUser;
use Groupeat\Auth\Services\GenerateTokenForUser;
use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected function registerServices()
    {
        $this->app->bind('ActivateUser', function()
        {
            return new ActivateUser;
        });

        $this->app->bind('DeleteUser', function()
        {
            return new DeleteUser;
        });

        $this->app->bind('GenerateTokenForUser', function($app)
        {
            return new GenerateTokenForUser($app['tymon.jwt.auth']);
        });

        $this->app->bind('RegisterUser', function($app)
        {
            return new RegisterUser($app['mailer']);
        });
    }

}
