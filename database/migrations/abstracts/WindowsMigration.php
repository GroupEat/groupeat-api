<?php

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

abstract class RestaurantWindowsMigration extends Migration
{
    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('restaurant_id')->unsigned()->index();
            $this->addFieldsTo($table);

            $table->foreign('restaurant_id')->references('id')->on(RestaurantsMigration::TABLE);
        });
    }

    abstract protected function addFieldsTo(Blueprint $table);
}
