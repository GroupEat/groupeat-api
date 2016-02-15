<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Orders\Jobs\Abstracts\AddCustomerOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Values\JoinableDistanceInKms;
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
        AddressConstraints $deliveryAddressConstraints,
        JoinableDistanceInKms $aroundDistanceInKms
    ): Order {
        $deliveryAddress = $this->getDeliveryAddress($this->deliveryAddressData, $deliveryAddressConstraints->value());

        $this->assertJoinable();
        $this->assertCloseEnough(
            $deliveryAddress,
            $this->groupOrder->getAddressToCompareToForJoining(),
            $aroundDistanceInKms->value()
        );

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
