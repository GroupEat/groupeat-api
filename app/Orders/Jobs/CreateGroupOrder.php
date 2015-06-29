<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Jobs\Abstracts\AddCustomerOrder;

class CreateGroupOrder extends AddCustomerOrder
{
    private $foodRushInMinutes;

    /**
     * @param int      $foodRushInMinutes
     * @param Customer $customer
     * @param array    $productFormats
     * @param array    $deliveryAddressData
     * @param string   $comment
     */
    public function __construct(
        $foodRushInMinutes,
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
    ) {
        parent::__construct($customer, $productFormats, $deliveryAddressData, $comment);

        $this->foodRushInMinutes = (int) $foodRushInMinutes;
    }

    public function getFoodRushInMinutes()
    {
        return $this->foodRushInMinutes;
    }
}
