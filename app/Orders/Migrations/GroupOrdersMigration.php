<?php
namespace Groupeat\Orders\Migrations;

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
            $table->timestamp('createdAt')->index();
            $table->timestamp('updatedAt')->index();
            $table->timestamp('closedAt')->nullable()->index();
            $table->timestamp('endingAt')->index();
            $table->timestamp('confirmedAt')->nullable();
            $table->timestamp('preparedAt')->nullable();

            $table->foreign('restaurantId')->references('id')->on(RestaurantsMigration::TABLE);
        });
    }
}
