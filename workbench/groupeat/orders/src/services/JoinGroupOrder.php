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
     * @param string         $comment
     *
     * @return Order
     */
    public function call(
        GroupOrder $groupOrder,
        Customer $customer,
        ProductFormats $productFormats,
        array $deliveryAddressData,
        $comment = null
    )
    {
        $this->assertJoinable($groupOrder);
        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $this->assertCloseEnough($deliveryAddress, $groupOrder->getInitiatingOrder()->deliveryAddress);

        $order = $groupOrder->addOrder($customer, $productFormats, $deliveryAddress, $comment);

        $this->fireSuitableEventsFor($order, 'groupOrderHasBeenJoined');

        return $order;
    }

    private function assertJoinable(GroupOrder $groupOrder)
    {
        if (!$groupOrder->isJoinable())
        {
            throw new UnprocessableEntity(
                'groupOrderCannotBeJoined',
                "The {$groupOrder->toShortString()} cannot be joined anymore."
            );
        }
    }

}
