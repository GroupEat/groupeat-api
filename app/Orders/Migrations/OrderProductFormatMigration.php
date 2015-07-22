<?php
namespace Groupeat\Orders\Migrations;

use Groupeat\Restaurants\Migrations\ProductFormatsMigration;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderProductFormatMigration extends Migration
{
    protected $table = 'order_product_format';

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orderId')->index();
            $table->unsignedInteger('productFormatId');
            $table->tinyInteger('amount');

            $table->foreign('orderId')->references('id')->on($this->getTableFor(OrdersMigration::class));
            $table->foreign('productFormatId')->references('id')->on($this->getTableFor(ProductFormatsMigration::class));
        });
    }
}
