<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\Abstracts\GroupOrderValidationHandler;
use Groupeat\Orders\Values\ExternalOrderFoodrushInMinutes;

class PushExternalOrderHandler extends GroupOrderValidationHandler
{
    /**
     * @var int
     */
    private $foodrushInMinutes;

    public function __construct(ExternalOrderFoodrushInMinutes $foodrushInMinutes)
    {
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
