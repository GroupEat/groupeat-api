<?php

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductFormatTable extends Migration
{
    protected $table = 'order_product_format';

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orderId');
            $table->unsignedInteger('productFormatId');
            $table->tinyInteger('amount');

            $table->foreign('orderId')->references('id')->on($this->getTableFor(CreateOrdersTable::class));
            $table->foreign('productFormatId')->references('id')->on($this->getTableFor(CreateProductFormatsTable::class));
        });
    }
}
