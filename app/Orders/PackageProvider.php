<?php namespace Groupeat\Orders;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Groupeat\Orders\Values\DeliveryAddressConstraints;
use Groupeat\Orders\Values\ExternalOrderFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        MinimumFoodrushInMinutes::class => 'orders.minimum_foodrush_in_minutes',
        MaximumFoodrushInMinutes::class => 'orders.maximum_foodrush_in_minutes',
        MaximumPreparationTimeInMinutes::class => 'orders.maximum_preparation_time_in_minutes',
        JoinableDistanceInKms::class => 'orders.joinable_distance_in_kilometers',
        ExternalOrderFoodrushInMinutes::class => 'orders.external_order_foodrush_in_minutes',
    ];

    protected $routeEntities = [
        Order::class => 'order',
        GroupOrder::class => 'groupOrder',
    ];
}
