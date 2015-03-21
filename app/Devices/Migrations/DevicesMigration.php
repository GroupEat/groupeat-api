<?php
namespace Groupeat\Devices\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DevicesMigration extends Migration
{
    const TABLE = 'devices';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('hardwareId')->unique();
            $table->string('notificationToken');
            $table->unsignedInteger('operating_system_id');
            $table->string('operatingSystemVersion');
            $table->string('model');
            $table->float('latitude')->index();
            $table->float('longitude')->index();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('operating_system_id')->references('id')->on(OperatingSystemsMigration::TABLE);
        });
    }
}
