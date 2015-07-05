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
    const TABLE = 'notifications';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerId');
            $table->unsignedInteger('deviceId');
            $table->unsignedInteger('groupOrderId');
            $table->timestamp(Notification::CREATED_AT)->index();

            $table->foreign('customerId')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('deviceId')->references('id')->on(DevicesMigration::TABLE);
            $table->foreign('groupOrderId')->references('id')->on(GroupOrdersMigration::TABLE);
        });
    }
}
