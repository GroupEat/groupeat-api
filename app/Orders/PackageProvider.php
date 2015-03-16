<?php namespace Groupeat\Orders;

use Groupeat\Orders\Values\AroundDistanceInKms;
use Groupeat\Orders\Values\DeliveryAddressConstraints;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Support\Providers\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            MinimumFoodrushInMinutes::class,
            'orders.minimum_foodrush_in_minutes'
        );

        $this->bindValueFromConfig(
            MaximumFoodrushInMinutes::class,
            'orders.maximum_foodrush_in_minutes'
        );

        $this->bindValueFromConfig(
            MaximumPreparationTimeInMinutes::class,
            'orders.maximum_preparation_time_in_minutes'
        );

        $this->bindValueFromConfig(
            AroundDistanceInKms::class,
            'orders.around_distance_in_kilometers'
        );
    }
}
