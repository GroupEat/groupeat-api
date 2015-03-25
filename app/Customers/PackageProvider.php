<?php namespace Groupeat\Customers;

use Groupeat\Auth\Auth;
use Groupeat\Customers\Commands\Register;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Handlers\Events\SendGroupOrderHasBeenConfirmedMails;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Events\GroupOrderHasBeenConfirmed;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            AddressConstraints::class,
            'customers.address_constraints'
        );

        $this->app['router']->model('customer', Customer::class);
    }

    protected function bootPackage()
    {
        $this->app[Auth::class]->addUserType(new Customer);

        $this->listen(GroupOrderHasBeenConfirmed::class, SendGroupOrderHasBeenConfirmedMails::class);
    }
}
