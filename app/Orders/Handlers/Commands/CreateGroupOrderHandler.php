<?php
namespace Groupeat\Orders\Handlers\Commands;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Commands\CreateGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Handlers\Commands\Abstracts\GroupOrderValidation;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;

class CreateGroupOrderHandler extends GroupOrderValidation
{
    private $minimumFoodRushInMinutes;
    private $maximumFoodRushInMinutes;
    private $maximumPreparationTimeInMinutes;

    public function __construct(
        Dispatcher $events,
        MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms,
        AddressConstraints $deliveryAddressConstraints,
        MinimumFoodrushInMinutes $minimumFoodRushInMinutes,
        MaximumFoodrushInMinutes $maximumFoodRushInMinutes,
        MaximumPreparationTimeInMinutes $maximumPreparationTimeInMinutes
    ) {
        parent::__construct($events, $maximumDeliveryDistanceInKms, $deliveryAddressConstraints);

        $this->minimumFoodRushInMinutes = $minimumFoodRushInMinutes->value();
        $this->maximumFoodRushInMinutes = $maximumFoodRushInMinutes->value();
        $this->maximumPreparationTimeInMinutes = $maximumPreparationTimeInMinutes->value();
    }

    public function handle(CreateGroupOrder $command)
    {
        $productFormats = $command->getProductFormats();
        $foodRushInMinutes = $command->getFoodRushInMinutes();
        $deliveryAddressData = $command->getDeliveryAddressData();

        $this->guardAgainstInvalidFoodRushDuration($foodRushInMinutes);

        $restaurant = $productFormats->getRestaurant();
        $this->assertThatTheRestaurantWontCloseTooSoon($restaurant, $foodRushInMinutes);

        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $this->assertCloseEnough($deliveryAddress, $restaurant->address);

        $order = GroupOrder::createWith(
            $command->getCustomer(),
            $deliveryAddress,
            $productFormats,
            $foodRushInMinutes,
            $command->getComment()
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
        $minimumMinutes = $foodRushInMinutes + $this->maximumPreparationTimeInMinutes;
        $to = $now->copy()->addMinutes($minimumMinutes);

        $restaurant->assertOpened($now, $to);
    }
}
