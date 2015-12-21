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

    /**
     * @param Device      $device
     * @param Customer    $customer
     * @param string|null $platformVersion
     * @param string|null $notificationToken
     * @param string|null $notificationId
     * @param array|null  $location
     */
    public function __construct(
        Device $device,
        Customer $customer,
        $platformVersion,
        $notificationToken,
        $notificationId,
        $location
    ) {
        $this->device = $device;
        $this->customer = $customer;
        $this->platformVersion = $platformVersion;
        $this->notificationToken = $notificationToken;
        $this->notificationId = $notificationId;
        $this->location = $location;
    }

    /**
     * @return Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return string|null
     */
    public function getPlatformVersion()
    {
        return $this->platformVersion;
    }

    /**
     * @return string|null
     */
    public function getNotificationToken()
    {
        return $this->notificationToken;
    }

    /**
     * @return string|null
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @return array|null
     */
    public function getLocation()
    {
        return $this->location;
    }
}
