<?php namespace Groupeat\Customers;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider {

    protected $require = [self::ROUTES];


    public function boot()
    {
        $this->app->make('groupeat.auth')->addUserType(new Customer);

        parent::boot();
    }

}
