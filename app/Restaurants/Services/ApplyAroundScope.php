<?php
namespace Groupeat\Restaurants\Services;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Illuminate\Database\Eloquent\Builder;
use League\Period\Period;

class ApplyAroundScope
{
    private $maximumDeliveryDistanceInKms;

    public function __construct(MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms)
    {
        $this->maximumDeliveryDistanceInKms = $maximumDeliveryDistanceInKms->value();
    }

    /**
     * @param Builder $query
     * @param float   $latitude
     * @param float   $longitude
     * @param int     $distanceInKms Null to use the maximum delivery distance
     */
    public function call(Builder $query, $latitude, $longitude, $distanceInKms = null)
    {
        if (is_null($distanceInKms)) {
            $distanceInKms = $this->maximumDeliveryDistanceInKms;
        }

        $query->whereHas('address', function (Builder $subQuery) use ($latitude, $longitude, $distanceInKms) {
            $subQuery->aroundInKilometers($latitude, $longitude, $distanceInKms);
        });
    }
}
