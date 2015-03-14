<?php
namespace Groupeat\Orders\Services;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Services\Abstracts\GroupOrderValidation;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Restaurants\Values\MinimumOpeningDurationInMinutes;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Events\Dispatcher;

class CreateGroupOrder extends GroupOrderValidation
{
    private $minimumFoodRushInMinutes;
    private $maximumFoodRushInMinutes;
    private $minimumRemainingOpeningMinutes;

    public function __construct(
        Dispatcher $events,
        MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms,
        AddressConstraints $deliveryAddressConstraints,
        MinimumFoodrushInMinutes $minimumFoodRushInMinutes,
        MaximumFoodrushInMinutes $maximumFoodRushInMinutes,
        MinimumOpeningDurationInMinutes $minimumRemainingOpeningMinutes
    ) {
        parent::__construct($events, $maximumDeliveryDistanceInKms, $deliveryAddressConstraints);

        $this->minimumFoodRushInMinutes = $minimumFoodRushInMinutes->value();
        $this->maximumFoodRushInMinutes = $maximumFoodRushInMinutes->value();
        $this->minimumRemainingOpeningMinutes = $minimumRemainingOpeningMinutes->value();
    }

    /**
     * @param Customer       $customer
     * @param ProductFormats $productFormats
     * @param                $foodRushInMinutes
     * @param array          $deliveryAddressData
     * @param null           $comment
     *
     * @return Order
     */
    public function call(
        Customer $customer,
        ProductFormats $productFormats,
        $foodRushInMinutes,
        array $deliveryAddressData,
        $comment = null
    ) {
        $foodRushInMinutes = (int) $foodRushInMinutes;
        $this->guardAgainstInvalidFoodRushDuration($foodRushInMinutes);

        $restaurant = $productFormats->getRestaurant();
        $this->assertThatTheRestaurantWontCloseTooSoon($restaurant, $foodRushInMinutes);

        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $this->assertCloseEnough($deliveryAddress, $restaurant->address);

        $order = GroupOrder::createWith(
            $customer,
            $deliveryAddress,
            $productFormats,
            $foodRushInMinutes,
            $comment
        );

        $this->fireSuitableEvents($order, new GroupOrderHasBeenCreated($order));

        return $order;
    }

    private function guardAgainstInvalidFoodRushDuration($foodRushInMinutes)
    {
        if ($foodRushInMinutes < $this->minimumFoodRushInMinutes
            || $foodRushInMinutes > $this->maximumFoodRushInMinutes
        ) {
            throw new UnprocessableEntity(
                "invalidFoodRushDuration",
                "The FoodRush duration must be between "
                .$this->minimumFoodRushInMinutes.' and '
                .$this->maximumFoodRushInMinutes.' minutes, '
                .$foodRushInMinutes.' given.'
            );
        }
    }

    private function assertThatTheRestaurantWontCloseTooSoon(Restaurant $restaurant, $foodRushInMinutes)
    {
        $now = new Carbon();
        $minimumMinutes = max($this->minimumRemainingOpeningMinutes, $foodRushInMinutes);
        $to = $now->copy()->addMinutes($minimumMinutes);

        $restaurant->assertOpened($now, $to);
    }
}
