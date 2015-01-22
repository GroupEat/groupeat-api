<?php namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductsMigration extends Migration {

    const TABLE = 'products';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('restaurant_id')->unsigned()->index();
            $table->integer('type_id')->unsigned()->index();
            $table->string('name');
            $table->string('description');

            $table->foreign('restaurant_id')->references('id')->on(RestaurantsMigration::TABLE);
            $table->foreign('type_id')->references('id')->on(FoodTypesMigration::TABLE);
        });
    }

}
