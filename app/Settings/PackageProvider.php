<?php namespace Groupeat\Settings;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Services\InitializeCustomerSettings;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function bootPackage()
    {
        Customer::created(function (Customer $customer) {
            $this->app[InitializeCustomerSettings::class]->call($customer);
        });
    }
}
