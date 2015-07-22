<?php
namespace Groupeat\Notifications\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Devices\Migrations\DevicesMigration;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Orders\Migrations\GroupOrdersMigration;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationsMigration extends Migration
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

            $table->foreign('customerId')->references('id')->on($this->getTableFor(CustomersMigration::class));
            $table->foreign('deviceId')->references('id')->on($this->getTableFor(DevicesMigration::class));
            $table->foreign('groupOrderId')->references('id')->on($this->getTableFor(GroupOrdersMigration::class));
        });
    }
}
