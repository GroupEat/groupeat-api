<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Jobs\CreateGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\Abstracts\GroupOrderValidationHandler;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;
use League\Period\Period;

class CreateGroupOrderHandler extends GroupOrderValidationHandler
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

    public function handle(CreateGroupOrder $job)
    {
        $productFormats = $job->getProductFormats();
        $foodRushInMinutes = $job->getFoodRushInMinutes();
        $deliveryAddressData = $job->getDeliveryAddressData();

        $this->guardAgainstInvalidFoodRushDuration($foodRushInMinutes);

        $restaurant = $productFormats->getRestaurant();
        $this->assertThatTheRestaurantWontCloseTooSoon($restaurant, $foodRushInMinutes);

        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $this->assertCloseEnough($deliveryAddress, $restaurant->address);

        $order = GroupOrder::createWith(
            $job->getCustomer(),
            $deliveryAddress,
            $productFormats,
            $foodRushInMinutes,
            $job->getComment()
        );

        $this->events->fire(new GroupOrderHasBeenCreated($order));

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
        $start = Carbon::now()->addMinutes($foodRushInMinutes);
        $end = $start->copy()->addMinutes($this->maximumPreparationTimeInMinutes);

        $restaurant->assertOpened(new Period($start, $end));
    }
}
