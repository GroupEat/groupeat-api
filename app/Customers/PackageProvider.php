<?php namespace Groupeat\Customers;

use Groupeat\Auth\Auth;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Services\SendGroupOrderHasBeenConfirmedMails;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Events\GroupOrderHasBeenConfirmed;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $require = [self::ROUTES];

    public function register()
    {
        parent::register();

        $this->bindValueFromConfig(
            AddressConstraints::class,
            'customers.address_constraints'
        );
    }

    public function boot()
    {
        parent::boot();

        $this->app[Auth::class]->addUserType(new Customer);

        $this->listen(GroupOrderHasBeenConfirmed::class, SendGroupOrderHasBeenConfirmedMails::class);
    }
}
