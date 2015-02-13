<?php namespace Groupeat\Orders\Migrations;

use Groupeat\Restaurants\Migrations\RestaurantsMigration;
use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GroupOrdersMigration extends Migration {

    const TABLE = 'group_orders';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('restaurant_id')->index();
            $table->unsignedInteger('discountRate')->index()->default(0);
            $table->timestamps();
            $table->index(['created_at', 'updated_at']);
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamp('ending_at')->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('prepared_at')->nullable();

            $table->foreign('restaurant_id')->references('id')->on(RestaurantsMigration::TABLE);
        });
    }

}
