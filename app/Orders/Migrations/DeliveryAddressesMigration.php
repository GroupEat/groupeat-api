<?php
namespace Groupeat\Orders\Migrations;

use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class DeliveryAddressesMigration extends AddressesMigration
{
    const TABLE = 'delivery_addresses';

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('order_id')->unique();

        $table->foreign('order_id')->references('id')->on(OrdersMigration::TABLE);
    }
}
