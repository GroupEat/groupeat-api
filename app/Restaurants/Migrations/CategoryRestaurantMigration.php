<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CategoryRestaurantMigration extends Migration
{
    const TABLE = 'category_restaurant';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('categoryId')->index();
            $table->integer('restaurantId')->index();

            $table->foreign('categoryId')->references('id')->on(CategoriesMigration::TABLE);
            $table->foreign('restaurantId')->references('id')->on(RestaurantsMigration::TABLE);
        });
    }
}
