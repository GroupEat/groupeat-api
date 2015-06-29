<?php namespace Groupeat\Orders;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Values\AroundDistanceInKms;
use Groupeat\Orders\Values\DeliveryAddressConstraints;
use Groupeat\Orders\Values\ExternalOrderFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

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

        $this->bindValueFromConfig(
            ExternalOrderFoodrushInMinutes::class,
            'orders.external_order_foodrush_in_minutes'
        );

        $this->app['router']->model('order', Order::class);
        $this->app['router']->model('groupOrder', GroupOrder::class);
    }
}
