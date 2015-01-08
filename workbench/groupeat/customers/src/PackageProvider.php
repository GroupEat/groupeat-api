<?php namespace Groupeat\Customers;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Services\RegisterCustomer;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];


    public function register()
    {
        parent::register();

        $this->app->bind('RegisterCustomerService', function($app)
        {
            return new RegisterCustomer($app['RegisterUserService']);
        });
    }

    public function boot()
    {
        parent::boot();

        $this->app->make('groupeat.auth')->addUserType(new Customer);
    }

}
