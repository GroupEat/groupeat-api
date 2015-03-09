<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestaurantsMigration extends Migration
{
    const TABLE = 'restaurants';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phoneNumber', 25);
            $table->unsignedInteger('minimumOrderPrice');
            $table->tinyInteger('deliveryCapacity');
            $table->string('discountPrices');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }
}
