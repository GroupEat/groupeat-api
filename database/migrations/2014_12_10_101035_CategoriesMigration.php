<?php

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class CategoriesMigration extends Migration
{
    const TABLE = 'restaurant_categories';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
        });
    }
}
