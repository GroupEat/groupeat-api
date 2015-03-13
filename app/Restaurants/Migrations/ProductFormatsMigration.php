<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductFormatsMigration extends Migration
{
    const TABLE = 'product_formats';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->index();
            $table->string('name');
            $table->unsignedInteger('price');

            $table->foreign('product_id')->references('id')->on(ProductsMigration::TABLE);
        });
    }
}
