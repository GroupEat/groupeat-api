<?php
namespace Groupeat\Orders\Handlers\Commands;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Commands\JoinGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Orders\Handlers\Commands\Abstracts\GroupOrderValidation;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Exceptions\UnprocessableEntity;

class JoinGroupOrderHandler extends GroupOrderValidation
{
    public function handle(JoinGroupOrder $command)
    {
        $groupOrder = $command->getGroupOrder();
        $deliveryAddress = $this->getDeliveryAddress($command->getDeliveryAddressData());

        $this->assertJoinable($groupOrder);
        $this->assertCloseEnough($deliveryAddress, $groupOrder->getInitiatingOrder()->deliveryAddress);

        $order = $groupOrder->addOrder(
            $command->getCustomer(),
            $command->getProductFormats(),
            $deliveryAddress,
            $command->getComment()
        );

        $this->fireSuitableEvents($order, new GroupOrderHasBeenJoined($order));

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
