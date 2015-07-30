<?php

use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTagProductTable extends Migration
{
    protected $table = 'food_tag_product';

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('foodTagId');
            $table->unsignedInteger('productId');

            $table->foreign('foodTagId')->references('id')->on($this->getTableFor(CreateFoodTagsTable::class));
            $table->foreign('productId')->references('id')->on($this->getTableFor(CreateProductsTable::class));
        });
    }
}
