<?php namespace Groupeat\Orders\Entities;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Exceptions\Forbidden;

class GroupOrder extends Entity {

    public function getRules()
    {
        return [
            'restaurant_id' => 'required|integer',
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

    public static function assertNotExistingFor(Restaurant $restaurant)
    {
        $now = new Carbon;
        $model = new static;

        $alreadyExisting = $model->where($model->getTableField('created_at'), '<=', $now)
            ->where($model->getTableField('ending_at'), '>=', $now)
            ->where($model->getTableField('restaurant_id'), $restaurant->id)
            ->count();

        if ($alreadyExisting)
        {
            throw new Forbidden(
                'groupOrderAlreadyExisting',
                "A group order already exists for the {$restaurant->toShortString()}."
            );
        }
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
        $order = new Order;
        $order->customer()->associate($customer);
        $order->created_at = $order->freshTimestamp();

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
     * @param int $minutes
     *
     * @return $this
     */
    public function setFoodRushDurationInMinutes($minutes)
    {
        if (!$this->created_at)
        {
            $this->created_at = $this->freshTimestamp();
        }

        $this->ending_at = $this->created_at->addMinutes($minutes);

        return $this;
    }

}
