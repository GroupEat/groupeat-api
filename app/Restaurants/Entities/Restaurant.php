<?php
namespace Groupeat\Restaurants\Entities;

use Carbon\Carbon;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Restaurants\Services\ApplyAroundScope;
use Groupeat\Restaurants\Services\ApplyOpenedScope;
use Groupeat\Restaurants\Services\ComputeClosingAt;
use Groupeat\Restaurants\Support\DiscountRate;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasPhoneNumber;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Values\Abstracts\DistanceInKms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use League\Period\Period;
use Phaza\LaravelPostgis\Geometries\Point;
use SebastianBergmann\Money\EUR;
use SebastianBergmann\Money\Money;

class Restaurant extends Entity implements User
{
    use HasCredentials, HasPhoneNumber, SoftDeletes;

    protected $fillable = ['name', 'phoneNumber'];

    protected $casts = [
        'discountPolicy' => 'json',
    ];

    public function getRules()
    {
        return [
            'name' => 'required',
            'phoneNumber' => 'required',
            'discountPolicy' => 'required',
            'pictureUrl' => 'required|string',
            'minimumGroupOrderPrice' => 'required|integer',
            'deliveryCapacity' => 'required|integer',
            'rating' => 'required|integer|max:10',
        ];
    }

    protected static function boot()
    {
        parent::boot();
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

    public function scopeAround(Builder $query, Point $location, $distanceInKms = null)
    {
        app(ApplyAroundScope::class)->call($query, $location, $distanceInKms);
    }

    public function isOpened(Period $period)
    {
        return $this->opened($period)->where($this->getTableField('id'), $this->id)->exists();
    }

    public function assertOpened(Period $period)
    {
        if (!$this->isOpened($period)) {
            $start = Carbon::instance($period->getStartDate());
            $end = Carbon::instance($period->getEndDate());

            throw new UnprocessableEntity(
                'restaurantClosed',
                "The {$this->toShortString()} do not stay opened from $start to $end."
            );
        }
    }

    public function scopeOpened(Builder $query, Period $period)
    {
        app(ApplyOpenedScope::class)->call($query, $period);
    }

    /**
     * The discount policy of a restaurant is saved in the database as a
     * JSON object. The keys represent the price to reach to unlock the specific discount rate
     * stored in the value.
     *
     * Example: if the JSON object is {900: 0, 1000: 10, 2000: 20, 2500: 30, 3500: 40, 6000: 50},
     * it means that for 10e there will be a 10% discount, for 20e 20%, for 25e 30%,
     * for 35e 40% and for 60e 50%. From 0e to 9e there won't be any discount.
     * Between the given points, the discount rate increase linearly.
     */
    public function getDiscountRateFor(Money $rawPrice): DiscountRate
    {
        $policy = $this->discountPolicy;
        ksort($policy);
        $prices = array_keys($policy);
        $percentages = array_values($policy);

        foreach ($prices as $index => $amount) {
            if ($rawPrice->getAmount() <= $amount) {
                if ($index == 0) {
                    return new DiscountRate($percentages[$index]);
                } else {
                    $slope = ((float) ($percentages[$index] - $percentages[$index - 1]))
                        / ($amount - $prices[$index - 1]);

                    $offset = $percentages[$index] - $slope * $amount;

                    return new DiscountRate((int) round($slope * $rawPrice->getAmount() + $offset));
                }
            }
        }

        return new DiscountRate(end($percentages));
    }

    protected function getMinimumGroupOrderPriceAttribute()
    {
        return new EUR($this->attributes['minimumGroupOrderPrice']);
    }

    protected function getDeliveryCapacityAttribute()
    {
        return (int) $this->attributes['deliveryCapacity'];
    }

    protected function getMaximumDiscountRateAttribute()
    {
        $discounts = array_values($this->discountPolicy);
        sort($discounts);

        return end($discounts);
    }

    protected function getClosingAtAttribute()
    {
        return app(ComputeClosingAt::class)->call($this);
    }
}
