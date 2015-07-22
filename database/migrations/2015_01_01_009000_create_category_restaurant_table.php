<?php

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryRestaurantTable extends Migration
{
    protected $table = 'category_restaurant';

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('categoryId')->index();
            $table->integer('restaurantId')->index();

            $table->foreign('categoryId')->references('id')->on($this->getTableFor(CreateCategoriesTable::class));
            $table->foreign('restaurantId')->references('id')->on($this->getTableFor(CreateRestaurantsTable::class));
        });
    }
}
