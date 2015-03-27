<?php
namespace Groupeat\Orders\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersMigration extends Migration
{
    const TABLE = 'orders';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerId')->index();
            $table->unsignedInteger('groupOrderId')->index();
            $table->unsignedInteger('rawPrice');
            $table->text('comment')->nullable();
            $table->boolean('initiator')->index()->default(false);
            $table->timestamp('createdAt')->index();

            $table->foreign('customerId')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('groupOrderId')->references('id')->on(GroupOrdersMigration::TABLE);
        });
    }
}
