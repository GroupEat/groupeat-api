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
    private $model;

    /**
     * @param Customer        $customer
     * @param string          $UUID
     * @param string          $notificationToken
     * @param Platform        $platform
     * @param string          $model
     */
    public function __construct(
        Customer $customer,
        $UUID,
        $notificationToken,
        Platform $platform,
        $model
    ) {
        $this->customer = $customer;
        $this->UUID = $UUID;
        $this->notificationToken = $notificationToken;
        $this->platform = $platform;
        $this->model = $model;
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

    public function getModel()
    {
        return $this->model;
    }
}