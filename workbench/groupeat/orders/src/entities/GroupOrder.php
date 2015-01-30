<?php namespace Groupeat\Orders\Entities;

use Carbon\Carbon;
use Config;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Database\Eloquent\Builder;

class GroupOrder extends Entity {

    protected $dates = ['completed_at', 'ending_at', 'confirmed_at'];


    public function getRules()
    {
        return [
            'restaurant_id' => 'required|integer',
            'reduction' => 'required|numeric',
            'ending_at' => 'required',
        ];
    }

    /**
     * @param Customer        $customer
     * @param DeliveryAddress $address
     * @param ProductFormats  $productFormats
     * @param int             $foodRushDurationInMinutes
     *
     * @return Order
     */
    public static function createWith(
        Customer $customer,
        DeliveryAddress $address,
        ProductFormats $productFormats,
        $foodRushDurationInMinutes
    )
    {
        $time = new Carbon;
        $restaurant = $productFormats->getRestaurant();
        static::assertNotExistingFor($restaurant);
        $groupOrder = new static;
        $groupOrder->restaurant()->associate($restaurant);
        $groupOrder->setFoodRushDurationInMinutes($foodRushDurationInMinutes);

        return $groupOrder->addOrder($customer, $productFormats, $address);
    }

    /**
     * @return bool
     */
    public function isOpened(Carbon $time = null)
    {
        $time = $time ?: new Carbon;

        return empty($this->completed_at) && $time->lt($this->ending_at);
    }

    public function productFormatsQuery()
    {
        $model = new ProductFormat;

        return $model->whereHas('orders', function($query)
        {
            $query->whereHas('groupOrder', function($subQuery)
            {
                $groupOrder = $subQuery->getModel();

                $subQuery->where($groupOrder->getTableField('id'), $this->id);
            });
        });
    }

    public function orders()
    {
        return $this->hasMany('Groupeat\Orders\Entities\Order');
    }

    public function restaurant()
    {
        return $this->belongsTo('Groupeat\Restaurants\Entities\Restaurant');
    }

    /**
     * @param Customer        $customer
     * @param ProductFormats  $productFormats
     * @param DeliveryAddress $address
     *
     * @return Order
     */
    public function addOrder(Customer $customer, ProductFormats $productFormats, DeliveryAddress $address)
    {
        $customer->assertActivated("The {$customer->toShortString()} should be activated to place an order.");
        $this->assertMinimumOrderPriceReached($productFormats);
        list($totalAmount, $totalRawPrice) = $this->getTotalAmountAndRawPriceWith($productFormats);
        $this->assertMaximumCapacityNotExceeded($totalAmount);
        $this->computeAndSetReductionFrom($totalRawPrice);

        $order = new Order;
        $order->rawPrice = $productFormats->price();
        $order->customer()->associate($customer);
        $order->setCreatedAt($order->freshTimestamp());

        if (!$this->exists)
        {
            $order->initiator = true;
        }

        if ($totalAmount == $this->restaurant->deliveryCapacity)
        {
            $this->completed_at = $this->freshTimestamp();
        }

        dbTransaction(function() use ($order, $address, $productFormats)
        {
            $this->save();
            $order->groupOrder()->associate($this);
            $order->save();
            $order->deliveryAddress()->save($address);
            $productFormats->attachTo($order);
        });

        return $order;
    }

    public function scopeOpened(Builder $query, Carbon $time = null)
    {
        $time = $time ?: new Carbon;
        $model = $query->getModel();

        $query->whereNull('completed_at')
            ->where($model->getTableField('ending_at'), '>', $time);
    }

    public function scopeAround(Builder $query, $latitude, $longitude, $distanceInKms = null)
    {
        $distanceInKms = $distanceInKms ?: Config::get('orders::around_distance_in_kilometers');

        $query->whereHas('orders', function(Builder $subQuery) use ($latitude, $longitude, $distanceInKms)
        {
            $subQuery->where($subQuery->getModel()->getTableField('initiator'), true);

            $subQuery->whereHas('deliveryAddress',
                function(Builder $miniQuery) use ($latitude, $longitude, $distanceInKms)
                {
                    $table = $miniQuery->getModel()->getTable();

                    whereAroundInKms($miniQuery, $table, $latitude, $longitude, $distanceInKms);
                }
            );
        });
    }

    private static function assertNotExistingFor(Restaurant $restaurant)
    {
        $now = new Carbon;
        $model = new static;

        $alreadyExisting = $model->whereNull($model->getTableField('completed_at'))
            ->where($model->getTableField('created_at'), '<=', $now)
            ->where($model->getTableField('ending_at'), '>=', $now)
            ->where($model->getTableField('restaurant_id'), $restaurant->id)
            ->count();

        if ($alreadyExisting)
        {
            throw new UnprocessableEntity(
                'groupOrderAlreadyExisting',
                "A group order already exists for the {$restaurant->toShortString()}."
            );
        }
    }

    private function setFoodRushDurationInMinutes($minutes)
    {
        if (!$this->created_at)
        {
            $this->created_at = $this->freshTimestamp();
        }

        $this->ending_at = $this->created_at->copy()->addMinutes($minutes);
    }

    private function getTotalAmountAndRawPriceWith(ProductFormats $productFormats)
    {
        if ($this->exists)
        {
            $productFormats = $productFormats->mergeWith($this);
        }

        return [$productFormats->count(), $productFormats->price()];
    }

    private function computeAndSetReductionFrom($totalRawPrice)
    {
        $reductionPrices = json_decode($this->restaurant->reductionPrices, true);
        $reductionValues = Config::get('restaurants::reductionValues');

        foreach ($reductionPrices as $index => $price)
        {
            if ($totalRawPrice <= $price)
            {
                if ($index == 0)
                {
                    $this->reduction = $reductionValues[$index];
                }
                else
                {
                    $slope = ($reductionValues[$index] - $reductionValues[$index - 1])
                        / ($price - $reductionPrices[$index - 1]);
                    $offset = $reductionValues[$index] - $slope * $price;

                    $this->reduction = $slope * $totalRawPrice + $offset;
                }

                return;
            }
        }

        $this->reduction = end($reductionValues);
    }

    private function assertMaximumCapacityNotExceeded($totalAmount)
    {
        if ($totalAmount > $this->restaurant->deliveryCapacity)
        {
            throw new UnprocessableEntity(
                'restaurantDeliveryCapacityExceeded',
                "The {$this->restaurant->toShortString()} cannot deliver more than {$this->restaurant->deliveryCapacity} items in the same group order, $totalAmount items asked."
            );
        }
    }

    private function assertMinimumOrderPriceReached(ProductFormats $productFormats)
    {
        if ($productFormats->price() < $this->restaurant->minimumOrderPrice)
        {
            throw new UnprocessableEntity(
                'minimumOrderPriceNotReached',
                "The order price is {$productFormats->price()} but must be greater than {$this->restaurant->minimumOrderPrice}."
            );
        }
    }

}
