<?php namespace Groupeat\Customers\Migrations;

use Groupeat\Customers\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class CustomerAddressesMigration extends AddressesMigration {

    const TABLE = 'customer_addresses';


    protected function addFields(Blueprint $table)
    {
        $table->integer('customer_id')->unsigned()->index();
        $table->timestamps();

        $table->foreign('customer_id')->references('id')->on(CustomersMigration::TABLE);
    }

}
