<?php
namespace Groupeat\Restaurants\Services;

use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Illuminate\Database\Eloquent\Builder;
use Phaza\LaravelPostgis\Geometries\Point;

class ApplyAroundScope
{
    private $maximumDeliveryDistanceInKms;

    public function __construct(MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms)
    {
        $this->maximumDeliveryDistanceInKms = $maximumDeliveryDistanceInKms->value();
    }

    public function call(Builder $query, Point $location, float $distanceInKms = null)
    {
        if (is_null($distanceInKms)) {
            $distanceInKms = $this->maximumDeliveryDistanceInKms;
        }

        $query->whereHas('address', function (Builder $subQuery) use ($location, $distanceInKms) {
            $subQuery->withinKilometers($location, $distanceInKms);
        });
    }
}
