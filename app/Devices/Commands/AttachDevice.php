<?php
namespace Groupeat\Devices\Commands;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Platform;
use Groupeat\Support\Commands\Abstracts\Command;

class AttachDevice extends Command
{

    private $customer;
    private $UUID;
    private $notificationToken;
    private $platform;
    private $version;
    private $model;
    private $latitude;
    private $longitude;

    /**
     * @param Customer        $customer
     * @param string          $UUID
     * @param string          $notificationToken
     * @param Platform        $platform
     * @param string          $version
     * @param string          $model
     * @param float           $latitude
     * @param float           $longitude
     */
    public function __construct(
        Customer $customer,
        $UUID,
        $notificationToken,
        Platform $platform,
        $version,
        $model,
        $latitude,
        $longitude
    ) {
        $this->customer = $customer;
        $this->UUID = $UUID;
        $this->notificationToken = $notificationToken;
        $this->platform = $platform;
        $this->version = $version;
        $this->model = $model;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
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

    public function getVersion()
    {
        return $this->version;
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
