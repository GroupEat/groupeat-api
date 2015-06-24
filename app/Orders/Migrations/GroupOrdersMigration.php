<?php
namespace Groupeat\Orders\Migrations;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Restaurants\Migrations\RestaurantsMigration;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GroupOrdersMigration extends Migration
{
    const TABLE = 'group_orders';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('restaurantId')->index();
            $table->unsignedInteger('discountRate')->index()->default(0);
            $table->timestamp(GroupOrder::CREATED_AT)->index();
            $table->timestamp(GroupOrder::UPDATED_AT)->index();
            $table->timestamp(GroupOrder::CLOSED_AT)->nullable()->index();
            $table->timestamp(GroupOrder::ENDING_AT)->index();
            $table->timestamp(GroupOrder::CONFIRMED_AT)->nullable();
            $table->timestamp(GroupOrder::PREPARED_AT)->nullable();

            $table->foreign('restaurantId')->references('id')->on(RestaurantsMigration::TABLE);
        });
    }
}
