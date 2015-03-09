<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CategoryRestaurantMigration extends Migration
{
    const TABLE = 'category_restaurant';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->index();
            $table->integer('restaurant_id')->index();
        });
    }
}
