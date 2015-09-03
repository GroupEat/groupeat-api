<?php

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeDiscountPricesToDiscountPolicy extends Migration
{
    protected $entity = Restaurant::class;

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('discountPrices');
            $table->json('discountPolicy')->default('{}');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('discountPolicy');
            $table->string('discountPrices')->default('[]');
        });
    }
}
