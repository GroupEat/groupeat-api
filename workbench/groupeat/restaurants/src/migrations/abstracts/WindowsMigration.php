<?php namespace Groupeat\Restaurants\Migrations\Abstracts;

use Groupeat\Restaurants\Migrations\RestaurantsMigration;
use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class WindowsMigration extends Migration {

    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('restaurant_id')->unsigned()->index();
            $this->addFieldsTo($table);
            $table->time('starting_at')->index();
            $table->time('ending_at')->index();

            $table->foreign('restaurant_id')->references('id')->on(RestaurantsMigration::TABLE);
        });
    }

    abstract protected function addFieldsTo(Blueprint $table);

}
