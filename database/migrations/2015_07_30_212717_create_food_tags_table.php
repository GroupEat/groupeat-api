<?php

use Groupeat\Restaurants\Entities\FoodTag;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTagsTable extends Migration
{
    protected $entity = FoodTag::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
        });
    }
}
