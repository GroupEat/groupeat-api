<?php namespace Groupeat\Notifications;

use Groupeat\Notifications\Values\GcmApiKey;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            GcmApiKey::class,
            'notifications.keys.gcm'
        );
    }
}
