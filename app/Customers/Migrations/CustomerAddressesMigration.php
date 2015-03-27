<?php
namespace Groupeat\Customers\Migrations;

use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerAddressesMigration extends AddressesMigration
{
    const TABLE = 'customer_addresses';

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('customerId')->unique();
        $table->timestamp('createdAt')->index();
        $table->timestamp('updatedAt')->index();

        $table->foreign('customerId')->references('id')->on(CustomersMigration::TABLE);
    }
}
