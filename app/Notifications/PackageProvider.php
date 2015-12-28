<?php namespace Groupeat\Notifications;

use Groupeat\Notifications\Events\NotificationHasBeenReceived;
use Groupeat\Notifications\Listeners\SaveNotificationReception;
use Groupeat\Notifications\Listeners\SendNoisyJoinGroupOrderNotificationAfterSilentOne;
use Groupeat\Notifications\Listeners\SendNotificationsToCustomersOnGoupOrderCreation;
use Groupeat\Notifications\Values\ApnsCertificatePassphrase;
use Groupeat\Notifications\Values\ApnsCertificatePath;
use Groupeat\Notifications\Values\DeviceLocationFreshnessInMinutes;
use Groupeat\Notifications\Values\GcmKey;
use Groupeat\Notifications\Values\NotificationsEnabled;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        ApnsCertificatePassphrase::class => 'notifications.apns.certificate_passphrase',
        ApnsCertificatePath::class => 'notifications.apns.certificate_path',
        GcmKey::class => 'notifications.gcm.key',
        NotificationsEnabled::class => 'notifications.enabled',
        DeviceLocationFreshnessInMinutes::class => 'notifications.device_location_freshness_in_minutes',
    ];

    protected $listeners = [
        SaveNotificationReception::class => NotificationHasBeenReceived::class,
        SendNoisyJoinGroupOrderNotificationAfterSilentOne::class => NotificationHasBeenReceived::class,
        SendNotificationsToCustomersOnGoupOrderCreation::class => GroupOrderHasBeenCreated::class,
    ];
}
