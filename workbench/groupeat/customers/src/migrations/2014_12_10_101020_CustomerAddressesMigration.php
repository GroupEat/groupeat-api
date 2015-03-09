<?php
namespace Groupeat\Customers\Migrations;

use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class CustomerAddressesMigration extends AddressesMigration
{
    const TABLE = 'customer_addresses';

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('customer_id')->unique();
        $table->timestamps();
        $table->index(['created_at', 'updated_at']);

        $table->foreign('customer_id')->references('id')->on(CustomersMigration::TABLE);
    }
}
