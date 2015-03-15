<?php
namespace Groupeat\Restaurants\Entities;

use Carbon\Carbon;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Restaurants\Support\DiscountRate;
use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use SebastianBergmann\Money\EUR;
use SebastianBergmann\Money\Money;

class Restaurant extends Entity implements User
{
    use HasCredentials, SoftDeletes;

    private static $discountRates;
    private static $aroundDistanceInKms;
    private static $openingDurationInMinutes;

    protected $fillable = ['name', 'phoneNumber'];

    public function getRules()
    {
        return [
            'name' => 'required',
            'phoneNumber' => ['required', 'regex:/^0[0-9]([ .-]?[0-9]{2}){4}$/'],
            'minimumOrderPrice' => 'required|integer',
            'deliveryCapacity' => 'required|integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::$discountRates = config('restaurants.discountRates');
        static::$aroundDistanceInKms = config('restaurants.around_distance_in_kilometers');
        static::$openingDurationInMinutes = config('restaurants.opening_duration_in_minutes');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function openingWindows()
    {
        return $this->hasMany(OpeningWindow::class);
    }

    public function closingWindows()
    {
        return $this->hasMany(ClosingWindow::class);
    }

    public function scopeAround(Builder $query, $latitude, $longitude, $distanceInKms = null)
    {
        $distanceInKms = $distanceInKms ?: static::$aroundDistanceInKms;

        $query->whereHas('address', function (Builder $subQuery) use ($latitude, $longitude, $distanceInKms) {
            $subQuery->aroundInKilometers($latitude, $longitude, $distanceInKms);
        });
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return bool
     */
    public function isOpened(Carbon $from = null, Carbon $to = null)
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: $from->copy()->addMinutes(static::$openingDurationInMinutes);
        assertSameDay($from, $to);

        $hasClosingWindow = ! $this->closingWindows->filter(
            function ($closingWindow) use ($from, $to) {
                return $closingWindow->from <= $to && $closingWindow->to >= $from;
            }
        )->isEmpty();

        if ($hasClosingWindow) {
            return false;
        }

        return ! $this->openingWindows->filter(
            function ($openingWindow) use ($from, $to) {
                return $openingWindow->dayOfWeek == $from->dayOfWeek
                && $openingWindow->from <= $from
                && $openingWindow->to >= $to;
            }
        )->isEmpty();
    }

    public function assertOpened(Carbon $from = null, Carbon $to = null)
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: $from->copy()->addMinutes(static::$openingDurationInMinutes);
        assertSameDay($from, $to);

        if (!$this->isOpened($from, $to)) {
            throw new UnprocessableEntity(
                'restaurantClosed',
                "The {$this->toShortString()} is not opened from $from to $to."
            );
        }
    }

    public function scopeOpened(Builder $query, Carbon $from = null, Carbon $to = null)
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: $from->copy()->addMinutes(static::$openingDurationInMinutes);
        assertSameDay($from, $to);

        $query->whereHas('openingWindows', function (Builder $subQuery) use ($from, $to) {
            $openingWindow = $subQuery->getModel();

            $subQuery->where($openingWindow->getTableField('dayOfWeek'), $from->dayOfWeek)
                ->where($openingWindow->getTableField('from'), '<=', $from->toTimeString())
                ->where($openingWindow->getTableField('to'), '>=', $to->toTimeString());
        });

        $query->whereDoesntHave('closingWindows', function (Builder $subQuery) use ($from, $to) {
            $closingWindow = $subQuery->getModel();

            $subQuery->where($closingWindow->getTableField('from'), '<=', $to)
                ->where($closingWindow->getTableField('to'), '>=', $from);
        });
    }

    /**
     * @param Money $rawPrice
     *
     * @return DiscountRate
     */
    public function getDiscountRateFor(Money $rawPrice)
    {
        foreach ($this->discountPrices as $index => $amount) {
            if ($rawPrice->getAmount() <= $amount) {
                if ($index == 0) {
                    return new DiscountRate(static::$discountRates[$index]);
                } else {
                    $slope = ((float) (static::$discountRates[$index] - static::$discountRates[$index - 1]))
                        / ($amount - $this->discountPrices[$index - 1]);

                    $offset = static::$discountRates[$index] - $slope * $amount;

                    return new DiscountRate((int) round($slope * $rawPrice->getAmount() + $offset));
                }
            }
        }

        return new DiscountRate((int) end(static::$discountRates));
    }

    protected function getDiscountPricesAttribute()
    {
        return json_decode($this->attributes['discountPrices'], true);
    }

    protected function getMinimumOrderPriceAttribute()
    {
        return new EUR($this->attributes['minimumOrderPrice']);
    }

    protected function getDeliveryCapacityAttribute()
    {
        return (int) $this->attributes['deliveryCapacity'];
    }

    protected function getDiscountPolicyAttribute()
    {
        return array_combine($this->discountPrices, static::$discountRates);
    }
}
