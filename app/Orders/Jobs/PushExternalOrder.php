<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\Abstracts\AddOrder;
use Groupeat\Orders\Values\ExternalGroupOrderDurationInMinutes;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Values\PhoneNumber;
use Illuminate\Contracts\Events\Dispatcher;

class PushExternalOrder extends AddOrder
{
    private $restaurant;

    private $customerPhoneNumber;

    public function __construct(
        Restaurant $restaurant,
        array $productFormats,
        array $deliveryAddressData,
        string $comment,
        PhoneNumber $customerPhoneNumber = null
    ) {
        parent::__construct($productFormats, $deliveryAddressData, $comment);

        $this->restaurant = $restaurant;
        $this->customerPhoneNumber = $customerPhoneNumber;
    }

    public function handle(Dispatcher $events, ExternalGroupOrderDurationInMinutes $durationInMinutes): Order
    {
        $customer = Customer::addExternalCustomer($this->customerPhoneNumber);

        $order = GroupOrder::createWith(
            $customer,
            $this->deliveryAddress,
            $this->productFormats,
            Carbon::now()->addMinutes($durationInMinutes->value()),
            $this->comment
        );

        $events->fire(new GroupOrderHasBeenCreated($order));

        return $order;
    }
}
