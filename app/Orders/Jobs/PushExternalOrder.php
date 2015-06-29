<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Orders\Jobs\Abstracts\AddOrder;
use Groupeat\Restaurants\Entities\Restaurant;

class PushExternalOrder extends AddOrder
{
    private $restaurant;
    private $customerFirstName;
    private $customerLastName;
    private $customerPhoneNumber;

    /**
     * @param Restaurant $restaurant
     * @param string $customerFirstName
     * @param string $customerLastName
     * @param string  $customerPhoneNumber
     * @param array $productFormats
     * @param array $deliveryAddressData
     * @param null  $comment
     */
    public function __construct(
        Restaurant $restaurant,
        $customerFirstName,
        $customerLastName,
        $customerPhoneNumber,
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
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
