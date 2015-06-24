<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Entities\Address;
use Groupeat\Support\Migrations\Abstracts\AddressesMigration;
use Illuminate\Database\Schema\Blueprint;

class RestaurantAddressesMigration extends AddressesMigration
{
    const TABLE = 'restaurant_addresses';

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('restaurantId')->unique();
        $table->timestamp(Address::CREATED_AT);
        $table->timestamp(Address::UPDATED_AT);

        $table->foreign('restaurantId')->references('id')->on(RestaurantsMigration::TABLE);
    }
}
