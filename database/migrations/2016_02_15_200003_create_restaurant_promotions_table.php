<?php

use Groupeat\Restaurants\Entities\Promotion;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantPromotionsTable extends Migration
{
    protected $entity = Promotion::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('restaurantId')->index();
            $table->unsignedInteger('rawPriceThreshold')->index();
            $table->unsignedInteger('beneficiaryCount')->nullable();
            $table->string('name');

            $table->foreign('restaurantId')->references('id')->on($this->getTableFor(CreateRestaurantsTable::class));
        });
    }
}
