<?php
namespace Groupeat\Customers\Migrations;

use Groupeat\Customers\Entities\PredefinedAddress;
use Groupeat\Support\Migrations\Abstracts\AddressesMigration;

class PredefinedAddressesMigration extends AddressesMigration
{
    protected $entity = PredefinedAddress::class;
}
