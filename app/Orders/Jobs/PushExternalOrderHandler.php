<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Values\ExternalOrderFoodrushInMinutes;
use Illuminate\Contracts\Events\Dispatcher;

class PushExternalOrderHandler
{
    private $events;
    private $deliveryAddressConstraints;
    private $foodrushInMinutes;

    public function __construct(
        Dispatcher $events,
        AddressConstraints $addressConstraints,
        ExternalOrderFoodrushInMinutes $foodrushInMinutes
    ) {
        $this->events = $events;
        $this->deliveryAddressConstraints = $addressConstraints->value();
        $this->foodrushInMinutes = $foodrushInMinutes->value();
    }

    public function handle(PushExternalOrder $job)
    {
        $customer = Customer::addExternalCustomer(
            $job->getCustomerFirstName(),
            $job->getCustomerLastName(),
            $job->getCustomerPhoneNumber()
        );

        $deliveryAddress = new DeliveryAddress(array_merge(
            $job->getDeliveryAddressData(),
            $this->deliveryAddressConstraints
        ));

        $order = GroupOrder::createWith(
            $customer,
            $deliveryAddress,
            $job->getProductFormats(),
            $this->foodrushInMinutes,
            $job->getComment()
        );

        $this->events->fire(new GroupOrderHasBeenCreated($order));

        return $order;
    }
}
