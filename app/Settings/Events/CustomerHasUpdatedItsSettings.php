<?php
namespace Groupeat\Settings\Events;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Support\SettingBag;
use Groupeat\Support\Events\Abstracts\Event;

class CustomerHasUpdatedItsSettings extends Event
{
    private $customer;
    private $settingBag;

    public function __construct(Customer $customer, SettingBag $settingBag)
    {
        $this->customer = $customer;
        $this->settingBag = $settingBag;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getSettingBag()
    {
        return $this->settingBag;
    }


}
