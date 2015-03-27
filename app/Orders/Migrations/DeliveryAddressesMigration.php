<?php
namespace Groupeat\Orders\Migrations;

use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class DeliveryAddressesMigration extends AddressesMigration
{
    const TABLE = 'delivery_addresses';

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('orderId')->unique();

        $table->foreign('orderId')->references('id')->on(OrdersMigration::TABLE);
    }
}
