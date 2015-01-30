<?php namespace Groupeat\Orders\Services;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Services\Abstracts\GroupOrderValidation;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Exceptions\UnprocessableEntity;

class CreateGroupOrder extends GroupOrderValidation {

    /**
     * @var int
     */
    private $minimumFoodRushDurationInMinutes;

    /**
     * @var int
     */
    private $maximumFoodRushDurationInMinutes;

    /**
     * @var int
     */
    private $minimumRemainingOpeningMinutes;


    public function __construct(
        $maximumDeliveryDistanceInKms,
        array $deliveryAddressConstraints,
        $minimumFoodRushDurationInMinutes,
        $maximumFoodRushDurationInMinutes,
        $minimumRemainingOpeningMinutes
    )
    {
        parent::__construct($maximumDeliveryDistanceInKms, $deliveryAddressConstraints);

        $this->minimumFoodRushDurationInMinutes = (int) $minimumFoodRushDurationInMinutes;
        $this->maximumFoodRushDurationInMinutes = (int) $maximumFoodRushDurationInMinutes;
        $this->minimumRemainingOpeningMinutes = $minimumRemainingOpeningMinutes;
    }

    /**
     * @param Customer       $customer
     * @param ProductFormats $productFormats
     * @param int            $foodRushDurationInMinutes
     * @param array          $addressData
     *
     * @return Order
     */
    public function call(
        Customer $customer,
        ProductFormats $productFormats,
        $foodRushDurationInMinutes,
        array $deliveryAddressData
    )
    {
        $foodRushDurationInMinutes = (int) $foodRushDurationInMinutes;
        $this->guardAgainstInvalidFoodRushDuration($foodRushDurationInMinutes);

        $restaurant = $productFormats->getRestaurant();
        $this->assertThatTheRestaurantWontCloseTooSoon($restaurant, $foodRushDurationInMinutes);

        $deliveryAddress = $this->getDeliveryAddress($deliveryAddressData);
        $this->assertCloseEnough($deliveryAddress, $restaurant->address);

        return GroupOrder::createWith($customer, $deliveryAddress, $productFormats, $foodRushDurationInMinutes);
    }

    private function guardAgainstInvalidFoodRushDuration($foodRushDurationInMinutes)
    {
        if (
            $foodRushDurationInMinutes < $this->minimumFoodRushDurationInMinutes
            || $foodRushDurationInMinutes > $this->maximumFoodRushDurationInMinutes
        )
        {
            throw new UnprocessableEntity(
                "invalidFoodRushDuration",
                "The FoodRush duration must be between "
                . $this->minimumFoodRushDurationInMinutes . ' and '
                . $this->maximumFoodRushDurationInMinutes . ' minutes, '
                . $foodRushDurationInMinutes . ' given.'
            );
        }
    }

    private function assertThatTheRestaurantWontCloseTooSoon(Restaurant $restaurant, $foodRushDurationInMinutes)
    {
        $now = new Carbon;
        $minimumMinutes = max($this->minimumRemainingOpeningMinutes, $foodRushDurationInMinutes);
        $to = $now->copy()->addMinutes($minimumMinutes);

        $restaurant->assertOpened($now, $to);
    }

}
