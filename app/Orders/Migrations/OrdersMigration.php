<?php
namespace Groupeat\Orders\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersMigration extends Migration
{
    protected $entity = Order::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerId')->index();
            $table->unsignedInteger('groupOrderId')->index();
            $table->unsignedInteger('rawPrice');
            $table->text('comment')->nullable();
            $table->boolean('initiator')->index()->default(false);
            $table->timestamp(Order::CREATED_AT)->index();

            $table->foreign('customerId')->references('id')->on($this->getTableFor(CustomersMigration::class));
            $table->foreign('groupOrderId')->references('id')->on($this->getTableFor(GroupOrdersMigration::class));
        });
    }
}
