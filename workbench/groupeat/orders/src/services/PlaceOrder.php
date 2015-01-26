<?php namespace Groupeat\Orders\Services;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupedOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Illuminate\Support\Collection;

class PlaceOrder {

    /**
     * @var int
     */
    private $maximumFoodRushDurationInMinutes;

    /**
     * @var int
     */
    private $maximumDeliveryDistanceInKms;

    /**
     * @var int
     */
    private $minimumRemainingOpeningMinutes;

    /**
     * @var array
     */
    private $deliveryAddressConstraints;


    public function __construct(
        $maximumFoodRushDurationInMinutes,
        $maximumDeliveryDistanceInKms,
        $minimumRemainingOpeningMinutes,
        array $deliveryAddressConstraints
    )
    {
        $this->maximumFoodRushDurationInMinutes = (int) $maximumFoodRushDurationInMinutes;
        $this->maximumDeliveryDistanceInKms = (float) $maximumDeliveryDistanceInKms;
        $this->minimumRemainingOpeningMinutes = (int) $minimumRemainingOpeningMinutes;
        $this->deliveryAddressConstraints = $deliveryAddressConstraints;
    }

    /**
     * @param Customer $customer
     * @param array    $productFormats
     * @param int      $foodRushDurationInMinutes
     * @param array    $addressData
     */
    public function call(
        Customer $customer,
        array $productFormats,
        $foodRushDurationInMinutes,
        array $deliveryAddressData
    )
    {
        $now = Carbon::now();
        $foodRushDurationInMinutes = (int) $foodRushDurationInMinutes;

        $deliveryAddress = new DeliveryAddress(array_merge(
            $deliveryAddressData,
            $this->deliveryAddressConstraints
        ));

        $customer->assertActivated("The {$customer->toShortString()} should be activated to place an order.");
        $this->guardAgainstTooLongFoodRush($foodRushDurationInMinutes);
        $this->guardAgainstEmptyOrder($productFormats);

        $askedIds = array_keys($productFormats);
        $productFormatModels = ProductFormat::with('product.restaurant')->findMany($askedIds);

        $this->assertThatAllProductFormatsExist($productFormatModels, $askedIds);
        $restaurant = $this->assertThatAllTheProductFormatsBelongToTheSameRestaurant($productFormatModels);
        $this->assertThatTheRestaurantWontCloseTooSoon($restaurant, $now, $foodRushDurationInMinutes);
        $this->assertCloseEnough($deliveryAddress, $restaurant);

        $productFormatsSyncData = [];

        foreach ($productFormats as $id => $amount)
        {
            $productFormatsSyncData[(int) $id] = ['amount' => (int) $amount];
        }

        dbTransaction(function() use (
            $now,
            $customer,
            $productFormatsSyncData,
            $foodRushDurationInMinutes,
            $deliveryAddress
        )
        {
            $groupedOrder = new GroupedOrder;
            $groupedOrder->ending_at = $now->copy()->addMinutes($foodRushDurationInMinutes);
            $groupedOrder->save();

            $order = new Order;
            $order->customer()->associate($customer);
            $order->groupedOrder()->associate($groupedOrder);
            $order->created_at = $now;
            $order->save();
            $address = $order->deliveryAddress()->save($deliveryAddress);
            $order->productFormats()->sync($productFormatsSyncData);
        });
    }

    private function guardAgainstTooLongFoodRush($foodRushDurationInMinutes)
    {
        if ($foodRushDurationInMinutes > $this->maximumFoodRushDurationInMinutes)
        {
            throw new BadRequest(
                "foodRushTooLong",
                "The FoodRush duration should not exceed "
                . $this->maximumFoodRushDurationInMinutes . ', '
                . $foodRushDurationInMinutes . ' given.'
            );
        }
    }

    private function guardAgainstEmptyOrder(array $productFormats)
    {
        if (empty($productFormats) || (array_sum($productFormats) == 0))
        {
            throw new BadRequest(
                'emptyOrder',
                "An order must contains one or more product formats."
            );
        }
    }

    private function assertThatAllProductFormatsExist($productFormatModels, $askedIds)
    {
        $foundIds = $productFormatModels->lists('id');
        $missingIds = array_diff($askedIds, $foundIds);

        if (!empty($missingIds))
        {
            throw new NotFound(
                'unexistingProductFormats',
                "The products formats #" . implode(',', $missingIds) . " do not exist"
            );
        }
    }

    /**
     * @param $productFormatModels
     *
     * @return Restaurant
     */
    private function assertThatAllTheProductFormatsBelongToTheSameRestaurant($productFormatModels)
    {
        $restaurants = $productFormatModels->map(function ($productFormat)
        {
            return $productFormat->product->restaurant;
        })->unique();

        if ($restaurants->count() > 1)
        {
            throw new BadRequest(
                'productFormatsFromDifferentRestaurants',
                "An order should have products from one restaurant only."
            );
        }

        return $restaurants->first();
    }

    private function assertThatTheRestaurantWontCloseTooSoon(
        Restaurant $restaurant,
        Carbon $now,
        $foodRushDurationInMinutes
    )
    {
        $minimumMinutes = max($this->minimumRemainingOpeningMinutes, $foodRushDurationInMinutes);
        $to = $now->copy()->addMinutes($minimumMinutes);

        $restaurant->assertOpened($now, $to);
    }

    private function assertCloseEnough(DeliveryAddress $deliveryAddress, Restaurant $restaurant)
    {
        $distanceInKms = $deliveryAddress->distanceInKmsWith($restaurant->address);

        if ($distanceInKms > $this->maximumDeliveryDistanceInKms)
        {
            throw new BadRequest(
                'deliveryDistanceTooLong',
                'The distance between the given delivery address and the '
                . $restaurant->toShortString() . ' should be less than '
                . $this->maximumDeliveryDistanceInKms . ' kms, '
                . $distanceInKms . ' given.'
            );
        }
    }

}
