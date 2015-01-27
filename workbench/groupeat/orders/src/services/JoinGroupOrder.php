<?php namespace Groupeat\Orders\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Services\Abstracts\GroupOrderValidation;
use Groupeat\Orders\Support\ProductFormats;

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
        $this->assertActivatedCustomer($customer);
        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $this->assertCloseEnough($deliveryAddress, $productFormats->getRestaurant());

        return $groupOrder->addOrder($customer, $productFormats, $deliveryAddress);
    }

}
