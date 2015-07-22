<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductFormatsMigration extends Migration
{
    protected $entity = ProductFormat::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('productId')->index();
            $table->string('name');
            $table->unsignedInteger('price');

            $table->foreign('productId')->references('id')->on($this->getTableFor(ProductsMigration::class));
        });
    }
}
