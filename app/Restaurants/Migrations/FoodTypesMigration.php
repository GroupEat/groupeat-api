<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FoodTypesMigration extends Migration
{
    protected $entity = FoodType::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
        });
    }
}
