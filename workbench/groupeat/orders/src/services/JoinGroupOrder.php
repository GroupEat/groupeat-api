<?php namespace Groupeat\Orders\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\GroupOrder;
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
     * @return GroupOrder
     */
    public function call(
        GroupOrder $groupOrder,
        Customer $customer,
        ProductFormats $productFormats,
        array $deliveryAddressData
    )
    {
        if (!$groupOrder->isOpened())
        {
            throw new UnprocessableEntity(
                'groupOrderClosed',
                "The {$groupOrder->toShortString()} cannot be joined because it has ended."
            );
        }

        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $firstOrder = $groupOrder->orders()->oldest()->first();
        $this->assertCloseEnough($deliveryAddress, $firstOrder->deliveryAddress);

        return $groupOrder->addOrder($customer, $productFormats, $deliveryAddress);
    }

    private function assertStillOpened(GroupOrder $groupOrder)
    {

    }

}
