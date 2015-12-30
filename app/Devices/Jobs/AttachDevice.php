<?php
namespace Groupeat\Devices\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Platform;
use Groupeat\Support\Jobs\Abstracts\Job;

class AttachDevice extends Job
{
    private $customer;
    private $UUID;
    private $notificationToken;
    private $platform;
    private $platformVersion;
    private $model;
    private $location;

    public function __construct(
        Customer $customer,
        string $UUID,
        string $notificationToken,
        Platform $platform,
        string $platformVersion,
        string $model,
        array $location
    ) {
        $this->customer = $customer;
        $this->UUID = $UUID;
        $this->notificationToken = $notificationToken;
        $this->platform = $platform;
        $this->platformVersion = $platformVersion;
        $this->model = $model;
        $this->location = $location;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getUUID()
    {
        return $this->UUID;
    }

    public function getNotificationToken()
    {
        return $this->notificationToken;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function getPlatformVersion()
    {
        return $this->platformVersion;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
