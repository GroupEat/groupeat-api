<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Jobs\Abstracts\AddCustomerOrder;
use Groupeat\Orders\Entities\GroupOrder;

class JoinGroupOrder extends AddCustomerOrder
{
    private $groupOrder;

    /**
     * @param GroupOrder $groupOrder
     * @param Customer   $customer
     * @param array      $productFormats
     * @param array      $deliveryAddressData
     * @param string     $comment
     */
    public function __construct(
        GroupOrder $groupOrder,
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
    ) {
        parent::__construct($customer, $productFormats, $deliveryAddressData, $comment);

        $this->groupOrder = $groupOrder;
    }

    public function getGroupOrder()
    {
        return $this->groupOrder;
    }
}
