<?php namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Migrations\RestaurantsMigration;
use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestaurantAddressesMigration extends Migration {

    const TABLE = 'restaurant_adresses';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('restaurant_id')->unsigned()->index();
            $table->string('street');
            $table->string('city');
            $table->string('postcode');
            $table->string('state');
            $table->string('country');
            $table->float('latitude');
            $table->float('longitude');
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on(RestaurantsMigration::TABLE);
        });
    }

}
