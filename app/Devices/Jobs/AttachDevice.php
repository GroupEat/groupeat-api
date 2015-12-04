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

    /**
     * @param Customer        $customer
     * @param string          $UUID
     * @param string          $notificationToken
     * @param Platform        $platform
     * @param string          $platformVersion
     * @param string          $model
     * @param array|null  $location
     */
    public function __construct(
        Customer $customer,
        $UUID,
        $notificationToken,
        Platform $platform,
        $platformVersion,
        $model,
        $location
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
