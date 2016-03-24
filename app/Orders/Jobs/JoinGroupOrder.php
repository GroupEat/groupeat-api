<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Orders\Jobs\Abstracts\AddCustomerOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;

class JoinGroupOrder extends AddCustomerOrder
{
    private $groupOrder;

    public function __construct(
        GroupOrder $groupOrder,
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        string $comment
    ) {
        parent::__construct($customer, $productFormats, $deliveryAddressData, $comment);

        $this->groupOrder = $groupOrder;
    }

    public function handle(
        Dispatcher $events,
        AddressConstraints $deliveryAddressConstraints
    ): Order {
        $deliveryAddress = $this->getDeliveryAddress($this->deliveryAddressData, $deliveryAddressConstraints->value());

        $this->assertJoinable();

        if (!$this->groupOrder->isCloseEnoughToJoin($deliveryAddress->location)) {
            throw new UnprocessableEntity(
                'deliveryDistanceTooLong',
                "The delivery address is too far from the group order"
            );
        }

        $order = $this->groupOrder->addOrder(
            $this->customer,
            $this->productFormats,
            $deliveryAddress,
            $this->comment
        );

        $events->fire(new GroupOrderHasBeenJoined($order));

        return $order;
    }

    private function assertJoinable()
    {
        if (!$this->groupOrder->isJoinable()) {
            throw new UnprocessableEntity(
                'groupOrderCannotBeJoined',
                "The {$this->groupOrder->toShortString()} cannot be joined anymore."
            );
        }
    }
}
