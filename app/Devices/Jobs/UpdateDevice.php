<?php
namespace Groupeat\Devices\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Jobs\Abstracts\Job;

class UpdateDevice extends Job
{
    private $device;
    private $customer;
    private $platformVersion;
    private $notificationToken;
    private $notificationId;
    private $location;

    public function __construct(
        Device $device,
        Customer $customer,
        string $platformVersion,
        string $notificationToken,
        string $notificationId,
        array $location
    ) {
        $this->device = $device;
        $this->customer = $customer;
        $this->platformVersion = $platformVersion;
        $this->notificationToken = $notificationToken;
        $this->notificationId = $notificationId;
        $this->location = $location;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getPlatformVersion()
    {
        return $this->platformVersion;
    }

    public function getNotificationToken()
    {
        return $this->notificationToken;
    }

    public function getNotificationId()
    {
        return $this->notificationId;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
