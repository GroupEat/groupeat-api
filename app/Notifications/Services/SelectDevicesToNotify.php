<?php
namespace Groupeat\Notifications\Services;

use Carbon\Carbon;
use Closure;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Settings\Entities\CustomerSettings;

class SelectDevicesToNotify
{
    /**
     * @param GroupOrder $groupOrder
     *
     * @return \Illuminate\Support\Collection
     */
    public function call(GroupOrder $groupOrder)
    {
        $customerAlreadyInIds = $this->getCustomerAlreadyInIds($groupOrder);
        $concernedCustomerIds = $this->getConcernedCustomerIds($groupOrder);
        $customersToNotifyIds = array_diff($customerAlreadyInIds, $concernedCustomerIds);

        return Device::whereIn('customerId', $customersToNotifyIds)
            ->with('customer', 'platform')
            ->get();
    }

    private function getCustomerAlreadyInIds(GroupOrder $groupOrder)
    {
        return $groupOrder->orders->map(function (Order $order) {
            return $order->customer->id;
        })->all();
    }

    private function getConcernedCustomerIds(GroupOrder $groupOrder)
    {
        $query = CustomerSettings::query()
            ->where(CustomerSettings::NOTIFICATIONS_ENABLED, true)
            ->where(CustomerSettings::NO_NOTIFICATION_AFTER, '>', Carbon::now()->toTimeString());

        return $query->lists('customerId')->all();
    }
}
