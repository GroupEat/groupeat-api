<?php
namespace Groupeat\Restaurants\Entities;

use Carbon\Carbon;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Restaurants\Services\ApplyAroundScope;
use Groupeat\Restaurants\Services\ApplyOpenedScope;
use Groupeat\Restaurants\Support\DiscountRate;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasPhoneNumber;
use Groupeat\Support\Exceptions\UnprocessableEntity;
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

    public function getRules()
    {
        return [
            'name' => 'required',
            'phoneNumber' => 'required',
            'minimumOrderPrice' => 'required|integer',
            'deliveryCapacity' => 'required|integer',
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

    public function isOpened(Period $period = null)
    {
        return $this->opened($period)->where($this->getTableField('id'), $this->id)->exists();
    }

    public function assertOpened(Period $period = null)
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

    public function scopeOpened(Builder $query, Period $period = null)
    {
        app(ApplyOpenedScope::class)->call($query, $period);
    }

    /**
     * @param Money $rawPrice
     *
     * @return DiscountRate
     */
    public function getDiscountRateFor(Money $rawPrice)
    {
        $percentages = DiscountRate::PERCENTAGES;

        foreach ($this->discountPrices as $index => $amount) {
            if ($rawPrice->getAmount() <= $amount) {
                if ($index == 0) {
                    return new DiscountRate($percentages[$index]);
                } else {
                    $slope = ((float) ($percentages[$index] - $percentages[$index - 1]))
                        / ($amount - $this->discountPrices[$index - 1]);

                    $offset = $percentages[$index] - $slope * $amount;

                    return new DiscountRate((int) round($slope * $rawPrice->getAmount() + $offset));
                }
            }
        }

        return new DiscountRate(end($percentages));
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
        return array_combine($this->discountPrices, DiscountRate::PERCENTAGES);
    }
}
