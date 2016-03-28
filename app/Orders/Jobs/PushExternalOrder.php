<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\Abstracts\AddOrder;
use Groupeat\Orders\Values\ExternalOrderFoodrushInMinutes;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Values\PhoneNumber;
use Illuminate\Contracts\Events\Dispatcher;
use League\Period\Period;

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
        string $comment,
        PhoneNumber $customerPhoneNumber = null
    ) {
        parent::__construct($productFormats, $deliveryAddressData, $comment);

        $this->restaurant = $restaurant;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->customerPhoneNumber = $customerPhoneNumber;
    }

    public function handle(
        Dispatcher $events,
        AddressConstraints $addressConstraints,
        ExternalOrderFoodrushInMinutes $foodrushInMinutes
    ): Order {
        $customer = Customer::addExternalCustomer(
            $this->customerFirstName,
            $this->customerLastName,
            $this->customerPhoneNumber
        );

        $deliveryAddress = $this->getDeliveryAddress($this->deliveryAddressData, $addressConstraints->value());

        $start = Carbon::now();
        $end = $start->copy()->addMinutes($foodrushInMinutes->value());
        $order = GroupOrder::createWith(
            $customer,
            $deliveryAddress,
            $this->productFormats,
            new Period($start, $end),
            $this->comment
        );

        $events->fire(new GroupOrderHasBeenCreated($order));

        return $order;
    }
}
