<?php

use Groupeat\Customers\Entities\PredefinedAddress;
use Groupeat\Support\Migrations\Abstracts\AddressesMigration;

class CreatePredefinedAddressesTable extends AddressesMigration
{
    protected $entity = PredefinedAddress::class;
}
