<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\Abstracts\GroupOrderValidationHandler;
use Groupeat\Orders\Values\ExternalOrderFoodrushInMinutes;
use Groupeat\Support\Values\NullValue;
use Illuminate\Contracts\Events\Dispatcher;

class PushExternalOrderHandler extends GroupOrderValidationHandler
{
    /**
     * @var int
     */
    private $foodrushInMinutes;

    public function __construct(
        Dispatcher $events,
        AddressConstraints $addressConstraints,
        ExternalOrderFoodrushInMinutes $foodrushInMinutes
    ) {
        parent::__construct($events, $addressConstraints, new NullValue);

        $this->foodrushInMinutes = $foodrushInMinutes->value();
    }

    public function handle(PushExternalOrder $job)
    {
        $customer = Customer::addExternalCustomer(
            $job->getCustomerFirstName(),
            $job->getCustomerLastName(),
            $job->getCustomerPhoneNumber()
        );

        $deliveryAddress = $this->getDeliveryAddress($job->getDeliveryAddressData());

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
