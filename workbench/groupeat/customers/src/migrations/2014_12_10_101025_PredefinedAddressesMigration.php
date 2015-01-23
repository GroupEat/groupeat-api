<?php namespace Groupeat\Customers\Migrations;

use Groupeat\Customers\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class PredefinedAddressesMigration extends AddressesMigration {

    const TABLE = 'predefined_addresses';


    protected function addFields(Blueprint $table)
    {
        //
    }

}
