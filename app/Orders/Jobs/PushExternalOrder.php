<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Orders\Jobs\Abstracts\AddOrder;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Values\PhoneNumber;

class PushExternalOrder extends AddOrder
{
    private $restaurant;
    private $customerFirstName;
    private $customerLastName;

    private $customerPhoneNumber;

    public function __construct(
        Restaurant $restaurant,
        string $customerFirstName,
        string $customerLastName,
        array $productFormats,
        array $deliveryAddressData,
        PhoneNumber $customerPhoneNumber,
        string $comment
    ) {
        parent::__construct($productFormats, $deliveryAddressData, $comment);

        $this->restaurant = $restaurant;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->customerPhoneNumber = $customerPhoneNumber;
    }

    public function getRestaurant()
    {
        return $this->restaurant;
    }

    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }

    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }

    public function getCustomerPhoneNumber()
    {
        return $this->customerPhoneNumber;
    }
}
