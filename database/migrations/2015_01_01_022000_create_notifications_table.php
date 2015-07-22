<?php

use Groupeat\Customers\Migrations\CreateCustomersTable;
use Groupeat\Devices\Migrations\CreateDevicesTable;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Orders\Migrations\CreateGroupOrdersTable;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    protected $entity = Notification::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerId');
            $table->unsignedInteger('deviceId');
            $table->unsignedInteger('groupOrderId');
            $table->timestamp(Notification::CREATED_AT)->index();

            $table->foreign('customerId')->references('id')->on($this->getTableFor(CreateCustomersTable::class));
            $table->foreign('deviceId')->references('id')->on($this->getTableFor(CreateDevicesTable::class));
            $table->foreign('groupOrderId')->references('id')->on($this->getTableFor(CreateGroupOrdersTable::class));
        });
    }
}
