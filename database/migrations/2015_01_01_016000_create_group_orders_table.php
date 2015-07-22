<?php

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Restaurants\Migrations\CreateRestaurantsTable;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupOrdersTable extends Migration
{
    protected $entity = GroupOrder::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('restaurantId')->index();
            $table->unsignedInteger('discountRate')->index()->default(0);
            $table->timestamp(GroupOrder::CREATED_AT)->index();
            $table->timestamp(GroupOrder::UPDATED_AT)->index();
            $table->timestamp(GroupOrder::CLOSED_AT)->nullable()->index();
            $table->timestamp(GroupOrder::ENDING_AT)->index();
            $table->timestamp(GroupOrder::CONFIRMED_AT)->nullable();
            $table->timestamp(GroupOrder::PREPARED_AT)->nullable();

            $table->foreign('restaurantId')->references('id')->on($this->getTableFor(CreateRestaurantsTable::class));
        });
    }
}
