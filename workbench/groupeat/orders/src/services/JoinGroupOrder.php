<?php namespace Groupeat\Orders\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Services\Abstracts\GroupOrderValidation;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Exceptions\UnprocessableEntity;

class JoinGroupOrder extends GroupOrderValidation {

    /**
     * @param GroupOrder     $groupOrder
     * @param Customer       $customer
     * @param ProductFormats $productFormats
     * @param array          $deliveryAddressData
     *
     * @return Order
     */
    public function call(
        GroupOrder $groupOrder,
        Customer $customer,
        ProductFormats $productFormats,
        array $deliveryAddressData
    )
    {
        $this->assertStillOpened($groupOrder);
        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $this->assertCloseEnough($deliveryAddress, $groupOrder->getInitiatingOrder()->deliveryAddress);

        $order = $groupOrder->addOrder($customer, $productFormats, $deliveryAddress);

        $this->events->fire('groupOrderHasBeenJoined', [$order]);

        return $order;
    }

    private function assertStillOpened(GroupOrder $groupOrder)
    {
        if (!$groupOrder->isOpened())
        {
            throw new UnprocessableEntity(
                'groupOrderClosed',
                "The {$groupOrder->toShortString()} cannot be joined because it has ended."
            );
        }
    }

}
