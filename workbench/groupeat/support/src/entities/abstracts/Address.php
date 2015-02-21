<?php namespace Groupeat\Support\Entities\Abstracts;

use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Presenters\Address as AddressPresenter;
use Illuminate\Database\Eloquent\Builder;
use Treffynnon\Navigator;

abstract class Address extends Entity {

    protected $fillable = ['street', 'details', 'city', 'postcode', 'state', 'country', 'latitude', 'longitude'];


    public function getRules()
    {
        return [
            'street' => 'required',
            'city' => 'required',
            'postcode' => 'required|digits:5',
            'state' => 'required',
            'country' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    /**
     * @param Address $other
     *
     * @return float
     */
    public function distanceInKmsWith(Address $other)
    {
        return Navigator::getDistance(
            $this->latitude,
            $this->longitude,
            $other->latitude,
            $other->longitude
        ) / 1000;
    }

    public function scopeAroundInKilometers(Builder $query, $latitude, $longitude, $kilometers)
    {
        $table = $this->getTable();

        if (!is_numeric($latitude) || !is_numeric($longitude))
        {
            throw new Exception(
                'invalidCoordinates',
                "The latitude and longitude must be numeric values."
            );
        }

        if (!is_numeric($kilometers))
        {
            throw new Exception(
                'invalidDistance',
                "The kilometers must be a numeric value."
            );
        }

        $query->whereRaw('(2 * (3959 * ATAN2(
                SQRT(
                    POWER(SIN(RADIANS('.$latitude.' - "'.$table.'"."latitude") / 2), 2) +
                    COS(RADIANS("'.$table.'"."latitude")) *
                    COS(RADIANS('.$latitude.')) *
                    POWER(SIN(RADIANS('.$longitude.' - "'.$table.'"."longitude") / 2), 2)
                ),
                SQRT(1 - (
                        POWER(SIN(RADIANS('.$latitude.' - "'.$table.'"."latitude") / 2), 2) +
                        COS(RADIANS("'.$table.'"."latitude")) *
                        COS(RADIANS('.$latitude.')) *
                        POWER(SIN(RADIANS('.$longitude.' - "'.$table.'"."longitude") / 2), 2)
                    ))
            )) <= '.$kilometers.')');
    }

    public function getPresenter()
    {
        return new AddressPresenter($this);
    }

    protected function getPostcodeAttribute()
    {
        return (int) $this->attributes['postcode'];
    }

    protected function getLatitudeAttribute()
    {
        return (float) $this->attributes['latitude'];
    }

    protected function getLongitudeAttribute()
    {
        return (float) $this->attributes['longitude'];
    }

}
