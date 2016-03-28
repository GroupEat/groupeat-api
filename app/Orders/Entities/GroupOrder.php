<?php
namespace Groupeat\Orders\Entities;

use Carbon\Carbon;
use DB;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Events\GroupOrderHasBeenClosed;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Support\DiscountRate;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Database\Eloquent\Builder;
use League\Period\Period;
use Phaza\LaravelPostgis\Geometries\Point;
use SebastianBergmann\Money\Money;

class GroupOrder extends Entity
{
    const CLOSED_AT = 'closedAt';
    const ENDING_AT = 'endingAt';
    const CONFIRMED_AT = 'confirmedAt';
    const PREPARED_AT = 'preparedAt';

    private static $joinableDistanceInKms;
    private static $maximumDeliveryDistanceInKms;

    protected $dates = [self::CLOSED_AT, self::ENDING_AT, self::CONFIRMED_AT, self::PREPARED_AT];

    public function getRules()
    {
        return [
            'restaurantId' => 'required',
            'discountRate' => 'required|integer',
            self::ENDING_AT => 'required',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::$joinableDistanceInKms = app(JoinableDistanceInKms::class)->value();
        static::$maximumDeliveryDistanceInKms = app(MaximumDeliveryDistanceInKms::class)->value();
    }

    public static function createWith(
        Customer $customer,
        DeliveryAddress $address,
        ProductFormats $productFormats,
        Period $period,
        string $comment = ''
    ): Order {
        $restaurant = $productFormats->getRestaurant();
        static::assertNotExistingFor($restaurant);
        static::assertMinimumGroupOrderPriceReached($restaurant, $productFormats);
        $groupOrder = new static;
        $groupOrder->restaurant()->associate($restaurant);
        if (!$groupOrder->exists) {
            $groupOrder->createdAt = Carbon::instance($period->getStartDate());
        }
        $groupOrder->endingAt = Carbon::instance($period->getEndDate());

        return $groupOrder->addOrder($customer, $productFormats, $address, $comment);
    }

    public static function assertNotExistingFor(Restaurant $restaurant)
    {
        $model = new static;
        $now = $model->freshTimestamp();

        $alreadyExisting = $model->whereNull($model->getTableField('closedAt'))
            ->where($model->getTableField(self::CREATED_AT), '<=', $now)
            ->where($model->getTableField(self::ENDING_AT), '>=', $now)
            ->where($model->getTableField('restaurantId'), $restaurant->id)
            ->count();

        if ($alreadyExisting) {
            throw new UnprocessableEntity(
                'groupOrderAlreadyExisting',
                "A group order already exists for the {$restaurant->toShortString()}."
            );
        }
    }

    public function isJoinable(Carbon $time = null): bool
    {
        $time = $time ?: $this->freshTimestamp();

        return empty($this->closedAt) && $this->endingAt->gt($time);
    }

    public function productFormatsQuery()
    {
        $model = new ProductFormat;

        return $model->whereHas('orders', function ($query) {
            $query->whereHas('groupOrder', function ($subQuery) {
                $groupOrder = $subQuery->getModel();

                $subQuery->where($groupOrder->getTableField('id'), $this->id);
            });
        });
    }

    public function getInitiatingOrder(): Order
    {
        return $this->orders->filter(function ($order) {
            return $order->initiator;
        })->first();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function isMadeUpWithoutAnyOrder()
    {
        return $this->isMadeUp && $this->orders->count() == 0;
    }

    public function addOrder(
        Customer $customer,
        ProductFormats $productFormats,
        DeliveryAddress $address,
        string $comment = ''
    ): Order {
        $customer->assertActivated("The {$customer->toShortString()} should be activated to place an order.");
        $customer->assertNoMissingInformation();
        list($nbProductFormats, $totalRawPrice) = $this->getNbProductFormatsAndRawPriceWith($productFormats);
        $this->assertMaximumCapacityNotExceeded($nbProductFormats);
        $this->discountRate = $productFormats->getRestaurant()->getDiscountRateFor($totalRawPrice);

        $order = new Order;

        $order->comment = $comment;
        $order->rawPrice = $productFormats->totalPrice();
        $order->customer()->associate($customer);
        $order->setCreatedAt($order->freshTimestamp());

        // An existing group order can have zero orders if it is a made-up one.
        if (!$this->exists || $this->isMadeUpWithoutAnyOrder()) {
            $order->initiator = true;
        }

        $this->save();
        $order->groupOrder()->associate($this);
        $order->save();
        $order->deliveryAddress()->save($address);
        $productFormats->attachTo($order);

        if ($nbProductFormats == $this->restaurant->deliveryCapacity) {
            $this->close();
        }

        return $order;
    }

    public function close()
    {
        if ($this->closedAt) {
            throw new UnprocessableEntity(
                'groupOrderAlreadyClosed',
                'The ' . $this->toShortString() . ' has already been closed'
            );
        }

        $this->closedAt = $this->freshTimestamp();
        $this->save();

        event(new GroupOrderHasBeenClosed($this));
    }

    public function confirm(Carbon $preparedAt)
    {
        $this->confirmedAt = $this->freshTimestamp();
        $this->preparedAt = $preparedAt;
        $this->save();
    }

    public function isConfirmed()
    {
        return !is_null($this->confirmedAt);
    }

    // Return the amount of product formats that can still be added to the group order
    public function computeRemainingCapacity(): int
    {
        $nbProductFormats = DB::table((new Order)->productFormats()->getTable())
            ->whereIn('orderId', $this->orders()->lists('id'))
            ->sum('quantity');

        return $this->restaurant->deliveryCapacity - $nbProductFormats;
    }

    public function getTotalRawPriceAttribute(): Money
    {
        return sumPrices($this->orders->pluck('rawPrice'));
    }

    public function getTotalDiscountedPriceAttribute(): Money
    {
        return sumPrices($this->orders->pluck('discountedPrice'));
    }

    public function scopeJoinable(Builder $query, Carbon $time = null)
    {
        $time = $time ?: $this->freshTimestamp();
        $model = $query->getModel();

        $query->whereNull($model->getTableField(self::CLOSED_AT))
            ->where($model->getTableField(self::ENDING_AT), '>', $time);
    }

    public function scopeAround(Builder $query, Point $location)
    {
        $query->where(function (Builder $subQuery) use ($location) {
            $subQuery->orWhere(function (Builder $microQuery) use ($location) {
                 $microQuery
                     ->where('isMadeUp', true)
                     ->whereHas('restaurant', function (Builder $microQuery) use ($location) {
                         $microQuery->whereHas('address', function (Builder $picoQuery) use ($location) {
                             $picoQuery->withinKilometers($location, static::$maximumDeliveryDistanceInKms);
                         });
                     });
            });
            $subQuery->orWhereHas('orders', function (Builder $miniQuery) use ($location) {
                $miniQuery->where($miniQuery->getModel()->getTableField('initiator'), true);

                $miniQuery->whereHas(
                    'deliveryAddress',
                    function (Builder $microQuery) use ($location) {
                        $microQuery->withinKilometers($location, static::$joinableDistanceInKms);
                    }
                );
            });
        });
    }

    public function scopeUnconfirmed(Builder $query, int $sinceMinutes = 0)
    {
        $query->whereNotNull($this->getTableField(self::CLOSED_AT))
            ->whereNull($this->getTableField(self::CONFIRMED_AT));

        if ($sinceMinutes) {
            $query->where(
                $this->getTableField(self::CLOSED_AT),
                '<',
                $this->freshTimestamp()->subMinutes($sinceMinutes)
            );
        }
    }

    public function isCloseEnoughToJoin(Point $point): bool
    {
        if ($this->isMadeUpWithoutAnyOrder()) {
            return $this->restaurant->address->distanceInKmsWithPoint($point) < static::$maximumDeliveryDistanceInKms;
        }

        return $this->getInitiatingOrder()->deliveryAddress->distanceInKmsWithPoint($point) < static::$joinableDistanceInKms;
    }

    protected function getDiscountRateAttribute()
    {
        return new DiscountRate((int) $this->attributes['discountRate']);
    }

    protected function setDiscountRateAttribute(DiscountRate $discountRate)
    {
        // The discount rate should only increase.
        // It can also be create different than 0 in the case of a made-up group order
        $this->attributes['discountRate'] = max(
            empty($this->attributes['discountRate']) ? 0 : $this->attributes['discountRate'],
            $discountRate->toPercentage()
        );
    }

    private static function assertMinimumGroupOrderPriceReached(Restaurant $restaurant, ProductFormats $productFormats)
    {
        if ($productFormats->totalPrice()->lessThan($restaurant->minimumGroupOrderPrice)) {
            throw new UnprocessableEntity(
                'minimumGroupOrderPriceNotReached',
                "This order price is {$productFormats->totalPrice()->getAmount()} "
                . "but must be greater than {$restaurant->minimumGroupOrderPrice->getAmount()}."
            );
        }
    }

    private function getNbProductFormatsAndRawPriceWith(ProductFormats $productFormats)
    {
        if ($this->exists) {
            $productFormats = $productFormats->mergeWith($this);
        }

        return [$productFormats->count(), $productFormats->totalPrice()];
    }

    private function assertMaximumCapacityNotExceeded($nbProductFormats)
    {
        if ($nbProductFormats > $this->restaurant->deliveryCapacity) {
            throw new UnprocessableEntity(
                'restaurantDeliveryCapacityExceeded',
                "The {$this->restaurant->toShortString()} cannot deliver more than "
                . "{$this->restaurant->deliveryCapacity} items in the same group order, "
                . "{$nbProductFormats} items asked."
            );
        }
    }
}
