<?php
namespace Groupeat\Orders\Migrations;

use Groupeat\Restaurants\Migrations\ProductFormatsMigration;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderProductFormatMigration extends Migration
{
    const TABLE = 'order_product_format';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orderId')->index();
            $table->unsignedInteger('productFormatId');
            $table->tinyInteger('amount');

            $table->foreign('orderId')->references('id')->on(OrdersMigration::TABLE);
            $table->foreign('productFormatId')->references('id')->on(ProductFormatsMigration::TABLE);
        });
    }
}
