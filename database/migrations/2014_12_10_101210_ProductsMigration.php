<?php

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class ProductsMigration extends Migration
{
    const TABLE = 'products';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('restaurant_id')->index();
            $table->unsignedInteger('type_id')->index();
            $table->string('name');
            $table->string('description');

            $table->foreign('restaurant_id')->references('id')->on(RestaurantsMigration::TABLE);
            $table->foreign('type_id')->references('id')->on(FoodTypesMigration::TABLE);
        });
    }
}
