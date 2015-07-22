<?php
namespace Groupeat\Restaurants\Migrations\Abstracts;

use Groupeat\Restaurants\Migrations\CreateRestaurantsTable;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class CreateWindowsTable extends Migration
{
    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('restaurantId')->unsigned()->index();
            $this->addFieldsTo($table);

            $table->foreign('restaurantId')->references('id')->on($this->getTableFor(CreateRestaurantsTable::class));
        });
    }

    abstract protected function addFieldsTo(Blueprint $table);
}
