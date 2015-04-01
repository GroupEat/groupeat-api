<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;

class SelectDevicesToNotify
{
    /**
     * @param GroupOrder $groupOrder
     *
     * @return \Illuminate\Support\Collection
     */
    public function call(GroupOrder $groupOrder)
    {
        $customerAlreadyInIds = $groupOrder->orders->map(function (Order $order) {
            return $order->customer->id;
        })->all();

        $deviceModel = new Device();

        return $deviceModel
            ->whereNotIn($deviceModel->getTableField('customerId'), [])
            ->with('customer', 'platform')
            ->get();
    }
}
