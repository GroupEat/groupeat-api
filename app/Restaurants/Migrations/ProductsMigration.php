<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductsMigration extends Migration
{
    const TABLE = 'products';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('restaurantId')->index();
            $table->unsignedInteger('typeId')->index();
            $table->string('name');
            $table->string('description');

            $table->foreign('restaurantId')->references('id')->on(RestaurantsMigration::TABLE);
            $table->foreign('typeId')->references('id')->on(FoodTypesMigration::TABLE);
        });
    }
}
