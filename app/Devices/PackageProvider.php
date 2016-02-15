<?php namespace Groupeat\Devices;

use Groupeat\Auth\Events\UserHasRetrievedItsToken;
use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Psr\Log\LoggerInterface;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function bootPackage()
    {
        $this->addDeviceInLogContext();

        $this->app['router']->bind('device', function ($UUID) {
            return Device::findByUUIDorFail($UUID);
        });
    }

    private function addDeviceInLogContext()
    {
        $this->app[LoggerInterface::class]->getMonolog()->pushProcessor(function ($record) {
            $request = $this->app['request'];
            $deviceUUID = $request->header('X-Device-Id');

            if (!is_null($deviceUUID)) {
                $record['context'] = compact('deviceUUID');
            }

            return $record;
        });
    }
}
