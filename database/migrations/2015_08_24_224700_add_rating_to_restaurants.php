<?php

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRatingToRestaurants extends Migration
{
    protected $entity = Restaurant::class;

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->tinyInteger('rating')->default(8);
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
}
