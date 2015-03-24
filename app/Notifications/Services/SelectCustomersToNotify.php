<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;

class SelectCustomersToNotify
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

        $customerModel = new Customer;

        return $customerModel
            ->whereNotIn($customerModel->getTableField('id'), $customerAlreadyInIds)
            ->has('devices')
            ->get();
    }
}
