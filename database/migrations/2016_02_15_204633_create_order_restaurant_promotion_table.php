<?php

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRestaurantPromotionTable extends Migration
{
    protected $table = 'order_restaurant_promotion';

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orderId')->index();
            $table->unsignedInteger('restaurantPromotionId')->index();

            $table->foreign('orderId')->references('id')->on($this->getTableFor(CreateOrdersTable::class));
            $table->foreign('restaurantPromotionId')->references('id')->on($this->getTableFor(CreateRestaurantPromotionsTable::class));
        });
    }
}
