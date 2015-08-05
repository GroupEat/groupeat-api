<?php namespace Groupeat\Messaging;

use Groupeat\Messaging\Values\NexmoKey;
use Groupeat\Messaging\Values\NexmoSecret;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        NexmoKey::class => 'messaging.nexmo.key',
        NexmoSecret::class => 'messaging.nexmo.secret',
    ];
}
