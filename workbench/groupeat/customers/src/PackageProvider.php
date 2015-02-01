<?php namespace Groupeat\Customers;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Services\ChangeCustomerAddress;
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

        $this->app->bind('ChangeCustomerAddressService', function($app)
        {
            return new ChangeCustomerAddress($app['config']->get('customers::address_constraints'));
        });
    }

    public function boot()
    {
        parent::boot();

        $this->app['groupeat.auth']->addUserType(new Customer);
    }

}
