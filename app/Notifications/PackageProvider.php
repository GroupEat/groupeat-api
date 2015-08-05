<?php namespace Groupeat\Notifications;

use Groupeat\Notifications\Values\ApnsCertificate;
use Groupeat\Notifications\Values\GcmKey;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Notifications\Listeners\SendNotificationToCustomers;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            GcmKey::class,
            'notifications.gcmKey'
        );

        $this->app->instance(
            ApnsCertificate::class,
            new ApnsCertificate(
                $this->app['config']->get('notifications.apnsCertificatePath'),
                $this->app['config']->get('notifications.apnsCertificatePassphrase')
            )
        );
    }

    protected function bootPackage()
    {
        $this->listen(GroupOrderHasBeenCreated::class, SendNotificationToCustomers::class);
    }
}
