<?php namespace Groupeat\Orders;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\CloseGroupOrderAfterFoodrush;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Groupeat\Orders\Values\DeliveryAddressConstraints;
use Groupeat\Orders\Values\ExternalOrderFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MaximumOrderFlowInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Support\Jobs\DelayedJob;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        ExternalOrderFoodrushInMinutes::class => 'orders.external_order_foodrush_in_minutes',
        JoinableDistanceInKms::class => 'orders.joinable_distance_in_kilometers',
        MaximumFoodrushInMinutes::class => 'orders.maximum_foodrush_in_minutes',
        MaximumPreparationTimeInMinutes::class => 'orders.maximum_preparation_time_in_minutes',
        MaximumOrderFlowInMinutes::class => 'orders.maximum_order_flow_in_minutes',
        MinimumFoodrushInMinutes::class => 'orders.minimum_foodrush_in_minutes',
    ];

    protected $routeEntities = [
        Order::class => 'order',
        GroupOrder::class => 'groupOrder',
    ];

    protected function bootPackage()
    {
        $this->delayJobOn(function (GroupOrderHasBeenCreated $event) {
            $groupOrder = $event->getOrder()->groupOrder;

            return new DelayedJob(
                new CloseGroupOrderAfterFoodrush($groupOrder),
                $groupOrder->endingAt
            );
        });
    }
}
