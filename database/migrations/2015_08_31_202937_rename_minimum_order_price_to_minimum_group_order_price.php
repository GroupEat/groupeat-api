<?php

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameMinimumOrderPriceToMinimumGroupOrderPrice extends Migration
{
    protected $entity = Restaurant::class;

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            DB::statement(
                'ALTER TABLE '.$this->getTable().' RENAME COLUMN "minimumOrderPrice" TO "minimumGroupOrderPrice";'
            );
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            DB::statement(
                'ALTER TABLE '.$this->getTable().' RENAME COLUMN "minimumGroupOrderPrice" TO "minimumOrderPrice";'
            );
        });
    }
}
