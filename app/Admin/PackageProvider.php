<?php
namespace Groupeat\Admin;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Admin\Events\GroupOrderHasNotBeenConfirmed;
use Groupeat\Admin\Jobs\CheckGroupOrderConfirmation;
use Groupeat\Admin\Listeners\BroadcastOnSlack;
use Groupeat\Admin\Values\MaxConfirmationDurationInMinutes;
use Groupeat\Admin\Values\SlackBroadcastingEnabled;
use Groupeat\Auth\Auth;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Orders\Events\GroupOrderHasBeenClosed;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Support\Jobs\DelayedJob;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        SlackBroadcastingEnabled::class => 'admin.slack_broadcasting_enabled',
        MaxConfirmationDurationInMinutes::class => 'admin.max_confirmation_duration_in_minutes',
    ];

    protected function bootPackage()
    {
        $this->app[Auth::class]->addUserType(new Admin);

        $this->listen(
            [
                UserHasRegistered::class,
                GroupOrderHasBeenCreated::class,
                GroupOrderHasBeenJoined::class,
                GroupOrderHasNotBeenConfirmed::class,
            ],
            BroadcastOnSlack::class
        );

        $this->delayJobOn(function (GroupOrderHasBeenClosed $event) {
            $groupOrder = $event->getGroupOrder();
            $maxConfirmationDurationInMinutes = $this->app[MaxConfirmationDurationInMinutes::class]->value();

            return new DelayedJob(
                new CheckGroupOrderConfirmation($groupOrder),
                $groupOrder->closedAt->copy()->addMinutes($maxConfirmationDurationInMinutes)
            );
        });
    }
}
