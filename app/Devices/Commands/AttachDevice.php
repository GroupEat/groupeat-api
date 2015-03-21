<?php
namespace Groupeat\Devices\Commands;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\OperatingSystem;
use Groupeat\Support\Commands\Abstracts\Command;

class AttachDevice extends Command
{

    private $customer;
    private $hardwareId;
    private $notificationToken;
    private $operatingSystem;
    private $operatingSystemVersion;
    private $model;
    private $latitude;
    private $longitude;

    /**
     * @param Customer        $customer
     * @param string          $hardwareId
     * @param string          $notificationToken
     * @param OperatingSystem $operatingSystem
     * @param string          $operatingSystemVersion
     * @param string          $model
     * @param float           $latitude
     * @param float           $longitude
     */
    public function __construct(
        Customer $customer,
        $hardwareId,
        $notificationToken,
        OperatingSystem $operatingSystem,
        $operatingSystemVersion,
        $model,
        $latitude,
        $longitude
    ) {
        $this->customer = $customer;
        $this->hardwareId = $hardwareId;
        $this->notificationToken = $notificationToken;
        $this->operatingSystem = $operatingSystem;
        $this->operatingSystemVersion = $operatingSystemVersion;
        $this->model = $model;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getHardwareId()
    {
        return $this->hardwareId;
    }

    public function getNotificationToken()
    {
        return $this->notificationToken;
    }

    public function getOperatingSystem()
    {
        return $this->operatingSystem;
    }

    public function getOperatingSystemVersion()
    {
        return $this->operatingSystemVersion;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }
}
