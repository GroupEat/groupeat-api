<?php
namespace Groupeat\Orders\Http\V1;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Http\V1\Traits\CanAddOrder;
use Groupeat\Orders\Jobs\CreateGroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class OrdersController extends Controller
{
    use CanAddOrder;

    public function indexForCustomer(Customer $customer)
    {
        $this->auth->assertSame($customer);

        return $this->collectionResponse(Order::where('customerId', $customer->id)->get());
    }

    public function indexForGroupOrder(Customer $customer, GroupOrder $groupOrder)
    {
        $this->auth->assertSame($customer);

        $orders = $groupOrder->orders->filter(function (Order $order) use ($customer) {
            return $order->customerId == $customer->id;
        });

        return $this->collectionResponse($orders);
    }

    public function show(Order $order)
    {
        $this->assertCanBeSeen($order);

        return $this->itemResponse($order);
    }

    public function showDeliveryAddress(Order $order)
    {
        $this->assertCanBeSeen($order);

        return $this->itemResponse($order->deliveryAddress);
    }

    public function place()
    {
        return $this->addOrder(function ($productFormats, $deliveryAddressData, $comment) {
            $endingAt = $this->optionalJson('endingAt') ? new Carbon($this->optionalJson('endingAt')) : null;

            return new CreateGroupOrder(
                (int) $this->optionalJson('foodRushDurationInMinutes'),
                $endingAt,
                $this->auth->customer(),
                $productFormats,
                $deliveryAddressData,
                $comment
            );
        });
    }

    private function assertCanBeSeen(Order $order)
    {
        if (!$this->auth->isSame($order->groupOrder->restaurant)) {
            $this->auth->assertSame($order->customer);
        }
    }
}
