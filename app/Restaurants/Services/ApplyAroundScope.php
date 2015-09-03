<?php
namespace Groupeat\Restaurants\Services;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Illuminate\Database\Eloquent\Builder;
use League\Period\Period;
use Phaza\LaravelPostgis\Geometries\Point;

class ApplyAroundScope
{
    private $maximumDeliveryDistanceInKms;

    public function __construct(MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms)
    {
        $this->maximumDeliveryDistanceInKms = $maximumDeliveryDistanceInKms->value();
    }

    /**
     * @param Builder $query
     * @param Point   $location
     * @param float   $distanceInKms Null to use the maximum delivery distance
     */
    public function call(Builder $query, Point $location, $distanceInKms = null)
    {
        if (is_null($distanceInKms)) {
            $distanceInKms = $this->maximumDeliveryDistanceInKms;
        }

        $query->whereHas('address', function (Builder $subQuery) use ($location, $distanceInKms) {
            $subQuery->withinKilometers($location, $distanceInKms);
        });
    }
}
