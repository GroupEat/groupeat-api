<?php

use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeliveryAddressesTable extends AddressesMigration
{
    protected $entity = DeliveryAddress::class;

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('orderId')->unique();

        $table->foreign('orderId')->references('id')->on($this->getTableFor(CreateOrdersTable::class));
    }
}
