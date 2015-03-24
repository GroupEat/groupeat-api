<?php namespace Groupeat\Notifications;

use Groupeat\Notifications\Values\GcmApiKey;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Notifications\Handlers\Events\SendNotificationToCustomers;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            GcmApiKey::class,
            'notifications.keys.gcm'
        );
    }

    protected function bootPackage()
    {
        $this->listen(GroupOrderHasBeenCreated::class, SendNotificationToCustomers::class);
    }
}
