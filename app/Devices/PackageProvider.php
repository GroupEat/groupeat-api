<?php namespace Groupeat\Devices;

use Groupeat\Auth\Events\UserHasRetrievedItsToken;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Listeners\KeepDeviceOwnerUpToDate;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Psr\Log\LoggerInterface;

class PackageProvider extends WorkbenchPackageProvider
{
    protected $listeners = [
        UserHasRetrievedItsToken::class => KeepDeviceOwnerUpToDate::class,
    ];

    protected function bootPackage()
    {
        $this->addDeviceInLogContext();

        $this->app['router']->bind('device', function ($UUID) {
            return Device::findByUUID($UUID);
        });
    }

    private function addDeviceInLogContext()
    {
        $this->app[LoggerInterface::class]->pushProcessor(function ($record) {
            $request = $this->app['request'];
            $deviceUUID = $request->header('X-Device-Id');

            if (!is_null($deviceUUID)) {
                $record['context'] = compact('deviceUUID');
            }

            return $record;
        });
    }
}
