<?php namespace Groupeat\Notifications;

use Groupeat\Notifications\Values\ApnsCertificatePassphrase;
use Groupeat\Notifications\Values\ApnsCertificatePath;
use Groupeat\Notifications\Values\GcmKey;
use Groupeat\Notifications\Values\NotificationsEnabled;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Groupeat\Notifications\Listeners\SendNotificationToCustomers;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        NotificationsEnabled::class => 'notifications.enabled',
        GcmKey::class => 'notifications.gcm.key',
        ApnsCertificatePath::class => 'notifications.apns.certificatePath',
        ApnsCertificatePassphrase::class => 'notifications.apns.certificatePassphrase',
    ];

    protected $listeners = [
        SendNotificationToCustomers::class => GroupOrderHasBeenCreated::class,
    ];
}
