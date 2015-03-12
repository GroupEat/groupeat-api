<?php

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class OrderProductFormatMigration extends Migration
{
    const TABLE = 'order_product_format';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->index();
            $table->unsignedInteger('product_format_id');
            $table->tinyInteger('amount');

            $table->foreign('order_id')->references('id')->on(OrdersMigration::TABLE);
            $table->foreign('product_format_id')->references('id')->on(ProductFormatsMigration::TABLE);
        });
    }
}
