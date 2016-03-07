<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\Abstracts\AddCustomerOrder;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;
use League\Period\Period;

class CreateGroupOrder extends AddCustomerOrder
{
    private $foodRushInMinutes;

    public function __construct(
        int $foodRushInMinutes,
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        string $comment
    ) {
        parent::__construct($customer, $productFormats, $deliveryAddressData, $comment);

        $this->foodRushInMinutes = $foodRushInMinutes;
    }

    public function handle(
        Dispatcher $events,
        AddressConstraints $deliveryAddressConstraints,
        MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms,
        MinimumFoodrushInMinutes $minimumFoodRushInMinutes,
        MaximumFoodrushInMinutes $maximumFoodRushInMinutes
    ): Order {
        $this->guardAgainstInvalidFoodRushDuration($minimumFoodRushInMinutes->value(), $maximumFoodRushInMinutes->value());

        $restaurant = $this->productFormats->getRestaurant();
        $this->assertThatTheRestaurantWontCloseTooSoon($restaurant);
        $deliveryAddress = $this->getDeliveryAddress($this->deliveryAddressData, $deliveryAddressConstraints->value());
        $this->assertCloseEnough($deliveryAddress, $restaurant->address, $maximumDeliveryDistanceInKms->value());

        $order = GroupOrder::createWith(
            $this->customer,
            $deliveryAddress,
            $this->productFormats,
            $this->foodRushInMinutes,
            $this->comment
        );

        $events->fire(new GroupOrderHasBeenCreated($order));

        return $order;
    }

    private function guardAgainstInvalidFoodRushDuration(int $minimumFoodRushInMinutes, int $maximumFoodRushInMinutes)
    {
        if ($this->foodRushInMinutes < $minimumFoodRushInMinutes || $this->foodRushInMinutes > $maximumFoodRushInMinutes) {
            throw new UnprocessableEntity(
                "invalidFoodRushDuration",
                "The FoodRush duration must be between "
                .$minimumFoodRushInMinutes.' and '
                .$maximumFoodRushInMinutes.' minutes, '
                .$this->foodRushInMinutes.' given.'
            );
        }
    }

    private function assertThatTheRestaurantWontCloseTooSoon(Restaurant $restaurant)
    {
        $start = Carbon::now()->addMinutes($this->foodRushInMinutes);
        $end = $start->copy()->addSecond();

        $restaurant->assertOpened(new Period($start, $end));
    }
}
