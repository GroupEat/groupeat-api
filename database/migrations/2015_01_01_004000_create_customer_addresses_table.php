<?php

use Groupeat\Customers\Entities\Address;
use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressesTable extends AddressesMigration
{
    protected $entity = Address::class;

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('customerId')->unique();
        $table->timestamp(Address::CREATED_AT)->index();
        $table->timestamp(Address::UPDATED_AT)->index();

        $table->foreign('customerId')->references('id')->on($this->getTableFor(CreateCustomersTable::class));
    }
}
