<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Jobs\JoinGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Orders\Jobs\Abstracts\GroupOrderValidationHandler;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Orders\Values\AroundDistanceInKms;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;

class JoinGroupOrderHandler extends GroupOrderValidationHandler
{
    public function __construct(
        Dispatcher $events,
        AddressConstraints $deliveryAddressConstraints,
        AroundDistanceInKms $aroundDistanceInKms
    ) {
        parent::__construct($events, $deliveryAddressConstraints, $aroundDistanceInKms);
    }

    public function handle(JoinGroupOrder $job)
    {
        $groupOrder = $job->getGroupOrder();
        $deliveryAddress = $this->getDeliveryAddress($job->getDeliveryAddressData());

        $this->assertJoinable($groupOrder);
        $this->assertCloseEnough($deliveryAddress, $groupOrder->getInitiatingOrder()->deliveryAddress);

        $order = $groupOrder->addOrder(
            $job->getCustomer(),
            $job->getProductFormats(),
            $deliveryAddress,
            $job->getComment()
        );

        $this->events->fire(new GroupOrderHasBeenJoined($order));

        return $order;
    }

    private function assertJoinable(GroupOrder $groupOrder)
    {
        if (!$groupOrder->isJoinable()) {
            throw new UnprocessableEntity(
                'groupOrderCannotBeJoined',
                "The {$groupOrder->toShortString()} cannot be joined anymore."
            );
        }
    }
}
