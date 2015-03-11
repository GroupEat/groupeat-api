<?php

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class OrdersMigration extends Migration
{
    const TABLE = 'orders';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->index();
            $table->unsignedInteger('group_order_id')->index();
            $table->unsignedInteger('rawPrice');
            $table->text('comment')->nullable();
            $table->boolean('initiator')->index()->default(false);
            $table->timestamp('created_at')->index();

            $table->foreign('customer_id')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('group_order_id')->references('id')->on(GroupOrdersMigration::TABLE);
        });
    }
}
