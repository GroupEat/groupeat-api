<?php
namespace Groupeat\Admin;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Admin\Services\ShareNoteworthyEventsOnSlack;
use Groupeat\Admin\Values\MaxConfirmationDurationInMinutes;
use Groupeat\Auth\Auth;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $configValues = [
        MaxConfirmationDurationInMinutes::class => 'admin.max_confirmation_duration_in_minutes',
    ];

    protected function bootPackage()
    {
        $this->app[Auth::class]->addUserType(new Admin);

        foreach (ShareNoteworthyEventsOnSlack::EVENT_CLASSES as $eventClass) {
            $this->listen($eventClass, ShareNoteworthyEventsOnSlack::class, 'share');
        }
    }
}
