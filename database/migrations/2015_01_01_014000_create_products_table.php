<?php

use Groupeat\Restaurants\Entities\Product;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    protected $entity = Product::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('restaurantId')->index();
            $table->unsignedInteger('typeId')->index();
            $table->string('name');
            $table->string('description');

            $table->foreign('restaurantId')->references('id')->on($this->getTableFor(CreateRestaurantsTable::class));
            $table->foreign('typeId')->references('id')->on($this->getTableFor(CreateFoodTypesTable::class));
        });
    }
}
