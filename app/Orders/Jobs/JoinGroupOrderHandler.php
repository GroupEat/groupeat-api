<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Jobs\JoinGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Orders\Jobs\Abstracts\GroupOrderValidation;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Exceptions\UnprocessableEntity;

class JoinGroupOrderHandler extends GroupOrderValidation
{
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
