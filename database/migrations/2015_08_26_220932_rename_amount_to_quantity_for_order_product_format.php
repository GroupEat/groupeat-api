<?php

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameAmountToQuantityForOrderProductFormat extends Migration
{
    protected $table = 'order_product_format';

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->renameColumn('amount', 'quantity');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->renameColumn('quantity', 'amount');
        });
    }
}
