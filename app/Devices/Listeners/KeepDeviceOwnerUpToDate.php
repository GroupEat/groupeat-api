<?php
namespace Groupeat\Devices\Listeners;

use Groupeat\Auth\Events\UserHasRetrievedItsToken;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Services\ChangeDeviceOwner;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class KeepDeviceOwnerUpToDate
{
    private $deviceUUID;
    private $logger;
    private $changeDeviceOwner;

    public function __construct(
        Request $request,
        LoggerInterface $logger,
        ChangeDeviceOwner $changeDeviceOwner
    ) {
        $this->deviceUUID = $request->header('X-Device-Id');
        $this->logger = $logger;
        $this->changeDeviceOwner = $changeDeviceOwner;
    }

    public function handle(UserHasRetrievedItsToken $event)
    {
        if ($event->getUser() instanceof Customer && !is_null($this->deviceUUID)) {
            $device = Device::where('UUID', $this->deviceUUID)->first();

            if (!is_null($device)) {
                $this->changeDeviceOwner->call($device, $event->getUser());
            } else {
                $this->logger->warning('The device #' . $this->deviceUUID . ' should exist');
            }
        }
    }
}
