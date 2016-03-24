<?php

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsMadeUpFieldToGroupOrders extends Migration
{
    protected $entity = GroupOrder::class;

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->boolean('isMadeUp')->default(false);
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('isMadeUp');
        });
    }
}
