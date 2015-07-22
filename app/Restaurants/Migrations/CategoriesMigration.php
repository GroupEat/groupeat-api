<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Entities\Category;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CategoriesMigration extends Migration
{
    protected $entity = Category::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
        });
    }
}
