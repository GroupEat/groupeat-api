<?php namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FoodTypesMigration extends Migration {

    const TABLE = 'food_types';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('label')->index()->unique();
        });
    }

}
