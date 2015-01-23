<?php namespace Groupeat\Customers\Entities;

use Groupeat\Customers\Migrations\CustomerAddressesMigration;
use Groupeat\Customers\Entities\Abstracts\Address as AbstractAddress;

class PredefinedAddress extends AbstractAddress {

    public $timestamps = false;

}
