<?php
namespace Groupeat\Settings\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Entities\CustomerSetting;
use Groupeat\Settings\Entities\Setting;

class InitializeCustomerSettings
{
    public function call(Customer $customer)
    {
        Setting::all()->each(function (Setting $setting) use ($customer) {
            CustomerSetting::set($setting, $customer, $setting->default);
        });
    }
}
