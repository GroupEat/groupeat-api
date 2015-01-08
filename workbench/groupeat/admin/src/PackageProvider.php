<?php namespace Groupeat\Admin;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Admin\Services\LoginAdmin;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::FILTERS, self::ROUTES];


    public function register()
    {
        parent::register();

        $this->app->bind('LoginAdminService', function($app)
        {
            return new LoginAdmin(
                $app['groupeat.auth'],
                $app['session.store'],
                $app->isLocal()
            );
        });
    }

    public function boot()
    {
        parent::boot();

        $this->app->make('groupeat.auth')->addUserType(new Admin);
    }

}
