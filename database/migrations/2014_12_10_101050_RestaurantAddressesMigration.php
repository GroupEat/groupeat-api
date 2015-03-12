<?php

use Illuminate\Database\Schema\Blueprint;

class RestaurantAddressesMigration extends AddressesMigration
{
    const TABLE = 'restaurant_addresses';

    protected function addFields(Blueprint $table)
    {
        $table->unsignedInteger('restaurant_id')->unique();
        $table->timestamps();

        $table->foreign('restaurant_id')->references('id')->on(RestaurantsMigration::TABLE);
    }
}
