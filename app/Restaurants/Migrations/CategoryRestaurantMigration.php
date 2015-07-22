<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CategoryRestaurantMigration extends Migration
{
    protected $table = 'category_restaurant';

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('categoryId')->index();
            $table->integer('restaurantId')->index();

            $table->foreign('categoryId')->references('id')->on($this->getTableFor(CategoriesMigration::class));
            $table->foreign('restaurantId')->references('id')->on($this->getTableFor(RestaurantsMigration::class));
        });
    }
}
