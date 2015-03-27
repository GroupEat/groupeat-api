<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductFormatsMigration extends Migration
{
    const TABLE = 'product_formats';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('productId')->index();
            $table->string('name');
            $table->unsignedInteger('price');

            $table->foreign('productId')->references('id')->on(ProductsMigration::TABLE);
        });
    }
}
