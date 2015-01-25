<?php namespace Groupeat\Orders\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersMigration extends Migration {

    const TABLE = 'orders';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->index();
            $table->unsignedInteger('grouped_order_id')->index();
            $table->timestamp('created_at')->index();

            $table->foreign('customer_id')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('grouped_order_id')->references('id')->on(GroupedOrdersMigration::TABLE);
        });
    }

}
