<?php namespace Groupeat\Customers;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Services\RegisterCustomer;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];


    public function boot()
    {
        $this->app->make('groupeat.auth')->addUserType(new Customer);

        parent::boot();
    }

    protected function registerServices()
    {
        $this->app->bind('RegisterCustomerService', function($app)
        {
            return new RegisterCustomer($app['RegisterUserService']);
        });
    }

}
