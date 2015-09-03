<?php

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    protected $entity = Restaurant::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phoneNumber', 15);
            $table->unsignedInteger('minimumOrderPrice');
            $table->tinyInteger('deliveryCapacity');
            $table->string('discountPrices');
            $table->string('pictureUrl');
            $table->timestamp(Restaurant::CREATED_AT);
            $table->timestamp(Restaurant::UPDATED_AT);
            $table->timestamp(Restaurant::DELETED_AT)->nullable()->index();
        });
    }
}
