<?php
namespace Groupeat\Settings\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Entities\CustomerSettings;

class InitializeCustomerSettings
{
    public function call(Customer $customer)
    {
        $setting = new CustomerSettings;
        $setting->customer()->associate($customer);
        $setting->notificationsEnabled = true;
        $setting->daysWithoutNotifying = 4;
        $setting->noNotificationAfter = '22:00:00';
        $setting->save();
    }
}
