<?php namespace Groupeat\Devices;

use Groupeat\Auth\Events\UserHasRetrievedItsToken;
use Groupeat\Devices\Handlers\Events\KeepDeviceOwnerUpToDate;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;
use Psr\Log\LoggerInterface;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function bootPackage()
    {
        $this->listen(UserHasRetrievedItsToken::class, KeepDeviceOwnerUpToDate::class);

        $this->addDeviceInLogContext();
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
