<?php
namespace Groupeat\Support\Entities\Traits;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Exceptions\Exception;
use Illuminate\Database\Eloquent\Builder;
use Treffynnon\Navigator;

trait HasPosition
{
    /**
     * @param Entity $other
     *
     * @return float
     */
    public function distanceInKmsWith(Entity $other)
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

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            throw new Exception(
                'invalidCoordinates',
                "The latitude and longitude must be numeric values."
            );
        }

        if (!is_numeric($kilometers)) {
            throw new Exception(
                'invalidDistance',
                "The kilometers must be a numeric value."
            );
        }

        $query->whereRaw(
            '(2 * (3959 * ATAN2(
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
            )) <= '.$kilometers.')'
        );
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
