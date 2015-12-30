<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Jobs\Abstracts\AddCustomerOrder;

class CreateGroupOrder extends AddCustomerOrder
{
    private $foodRushInMinutes;

    public function __construct(
        int $foodRushInMinutes,
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        string $comment
    ) {
        parent::__construct($customer, $productFormats, $deliveryAddressData, $comment);

        $this->foodRushInMinutes = $foodRushInMinutes;
    }

    public function getFoodRushInMinutes()
    {
        return $this->foodRushInMinutes;
    }
}
