<?php namespace Groupeat\Orders\Entities;

use Carbon\Carbon;
use Config;
use DB;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Support\DiscountRate;
use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GroupOrder extends Entity {

    private static $aroundDistanceInKms;

    protected $dates = ['completed_at', 'ending_at', 'confirmed_at', 'prepared_at'];


    public function getRules()
    {
        return [
            'restaurant_id' => 'required|integer',
            'discountRate' => 'required|integer',
            'ending_at' => 'required',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::$aroundDistanceInKms = Config::get('orders::around_distance_in_kilometers');
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
    public function isJoinable(Carbon $time = null)
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

    public function getInitiatingOrder()
    {
        return $this->orders->filter(function($order)
        {
            return $order->initiator;
        })->first();
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
        list($nbProductFormats, $totalRawPrice) = $this->getNbProductFormatsAndRawPriceWith($productFormats);
        $this->assertMaximumCapacityNotExceeded($nbProductFormats);
        $this->discountRate = $productFormats->getRestaurant()->getDiscountRateFor($totalRawPrice);

        $order = new Order;
        $order->rawPrice = $productFormats->totalPrice();
        $order->customer()->associate($customer);
        $order->setCreatedAt($order->freshTimestamp());

        if (!$this->exists)
        {
            $order->initiator = true;
        }

        if ($nbProductFormats == $this->restaurant->deliveryCapacity)
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

    /**
     * @param Carbon $preparedAt
     */
    public function confirm(Carbon $preparedAt)
    {
        $this->confirmed_at = $this->freshTimestamp();
        $this->prepared_at = $preparedAt;
        $this->save();
    }

    public function isConfirmed()
    {
        return !is_null($this->confirmed_at);
    }

    /**
     * @return int The number of product formats that can still be added to the group order
     */
    public function computeRemainingCapacity()
    {
        $nbProductFormats = DB::table((new Order)->productFormats()->getTable())
            ->whereIn('order_id', $this->orders()->lists('id'))
            ->sum('amount');

        return $this->restaurant->deliveryCapacity - $nbProductFormats;
    }

    /**
     * @return \SebastianBergmann\Money\Money
     */
    public function getTotalRawPriceAttribute()
    {
        return sumPrices(Collection::make($this->orders->lists('rawPrice')));
    }

    /**
     * @return \SebastianBergmann\Money\Money
     */
    public function getTotalDiscountedPriceAttribute()
    {
        return $this->discountRate->applyTo($this->totalRawPrice);
    }

    public function scopeJoinable(Builder $query, Carbon $time = null)
    {
        $time = $time ?: new Carbon;
        $model = $query->getModel();

        $query->whereNull('completed_at')
            ->where($model->getTableField('ending_at'), '>', $time);
    }

    public function scopeAround(Builder $query, $latitude, $longitude, $distanceInKms = null)
    {
        $distanceInKms = $distanceInKms ?: static::$aroundDistanceInKms;

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

    protected function getDiscountRateAttribute()
    {
        return new DiscountRate((int) $this->attributes['discountRate']);
    }

    protected function setDiscountRateAttribute(DiscountRate $discountRate)
    {
        $this->attributes['discountRate'] = $discountRate->toPercentage();
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

    private function getNbProductFormatsAndRawPriceWith(ProductFormats $productFormats)
    {
        if ($this->exists)
        {
            $productFormats = $productFormats->mergeWith($this);
        }

        return [$productFormats->count(), $productFormats->totalPrice()];
    }

    private function assertMaximumCapacityNotExceeded($nbProductFormats)
    {
        if ($nbProductFormats > $this->restaurant->deliveryCapacity)
        {
            throw new UnprocessableEntity(
                'restaurantDeliveryCapacityExceeded',
                "The {$this->restaurant->toShortString()} cannot deliver more than {$this->restaurant->deliveryCapacity} items in the same group order, {$nbProductFormats} items asked."
            );
        }
    }

    private function assertMinimumOrderPriceReached(ProductFormats $productFormats)
    {
        if ($productFormats->totalPrice()->lessThan($this->restaurant->minimumOrderPrice))
        {
            throw new UnprocessableEntity(
                'minimumOrderPriceNotReached',
                "The order price is {$productFormats->totalPrice()->getAmount()} but must be greater than {$this->restaurant->minimumOrderPrice->getAmount()}."
            );
        }
    }

}
