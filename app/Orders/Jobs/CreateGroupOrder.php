<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\Abstracts\AddCustomerOrder;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;
use League\Period\Period;

class CreateGroupOrder extends AddCustomerOrder
{
    private $endingAt;

    public function __construct(
        Carbon $endingAt,
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        string $comment
    ) {
        parent::__construct($customer, $productFormats, $deliveryAddressData, $comment);

        $this->endingAt = $endingAt;
    }

    public function handle(Dispatcher $events, MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms): Order
    {
        $restaurant = $this->productFormats->getRestaurant();
        $this->assertEndingAtInFirstOpenedWindow($restaurant, $this->endingAt);

        $this->assertCloseEnough($this->deliveryAddress, $restaurant->address, $maximumDeliveryDistanceInKms->value());

        $order = GroupOrder::createWith(
            $this->customer,
            $this->deliveryAddress,
            $this->productFormats,
            $this->endingAt,
            $this->comment
        );

        $events->fire(new GroupOrderHasBeenCreated($order));

        return $order;
    }

    private function assertEndingAtInFirstOpenedWindow(Restaurant $restaurant, Carbon $date)
    {
        // The end date is arbitrary, a week should be enough but it can be changed if needed.
        $openedWindows = $restaurant->getOpenedWindows(new Period(Carbon::now(), Carbon::now()->addWeek()));

        if ($openedWindows->isEmpty()) {
            $restaurant->throwClosedAt($date);
        }

        if (!$openedWindows[0]->contains($date)) {
            throw new BadRequest(
                'invalidEndingAt',
                "$date must be included in the first restaurant opened window: $openedWindows[0]"
            );
        }
    }

    private function assertCloseEnough(DeliveryAddress $deliveryAddress, Address $other, float $maximumDistanceInKms)
    {
        $distanceInKms = $deliveryAddress->distanceInKmsWith($other);

        if ($distanceInKms > $maximumDistanceInKms) {
            throw new UnprocessableEntity(
                'deliveryDistanceTooLong',
                "The delivery address is too far from the group order"
            );
        }
    }
}
