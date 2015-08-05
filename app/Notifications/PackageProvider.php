<?php namespace Groupeat\Notifications;

use Groupeat\Notifications\Values\ApnsCertificate;
use Groupeat\Notifications\Values\GcmKey;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Notifications\Listeners\SendNotificationToCustomers;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        GcmKey::class => 'notifications.gcmKey',
    ];

    protected function registerPackage()
    {
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
