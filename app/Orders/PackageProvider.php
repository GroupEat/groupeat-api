<?php namespace Groupeat\Orders;

use Groupeat\Orders\Events\GroupOrderHasBeenClosed;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Jobs\CloseGroupOrderIfNeeded;
use Groupeat\Orders\Listeners\GrantPromotions;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Groupeat\Orders\Values\ExternalGroupOrderDurationInMinutes;
use Groupeat\Orders\Values\MaximumOrderFlowInMinutes;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Support\Jobs\DelayedJob;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        ExternalGroupOrderDurationInMinutes::class => 'orders.external_group_order_duration_in_minutes',
        JoinableDistanceInKms::class => 'orders.joinable_distance_in_kilometers',
        MaximumPreparationTimeInMinutes::class => 'orders.maximum_preparation_time_in_minutes',
        MaximumOrderFlowInMinutes::class => 'orders.maximum_order_flow_in_minutes',
    ];

    protected $listeners = [
        GrantPromotions::class => GroupOrderHasBeenClosed::class,
    ];

    protected function bootPackage()
    {
        $this->delayJobOn(function (GroupOrderHasBeenCreated $event) {
            $groupOrder = $event->getOrder()->groupOrder;

            return new DelayedJob(
                new CloseGroupOrderIfNeeded($groupOrder),
                $groupOrder->endingAt
            );
        });
    }
}
