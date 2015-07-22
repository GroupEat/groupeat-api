<?php

use Groupeat\Restaurants\Entities\Address;
use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class CreateRestaurantAddressesTable extends AddressesMigration
{
    protected $entity = Address::class;

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('restaurantId')->unique();
        $table->timestamp(Address::CREATED_AT);
        $table->timestamp(Address::UPDATED_AT);

        $table->foreign('restaurantId')->references('id')->on($this->getTableFor(CreateRestaurantsTable::class));
    }
}
