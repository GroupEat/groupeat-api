<?php
namespace Groupeat\Devices\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Devices\Entities\Status;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StatusesMigration extends Migration
{
    const TABLE = 'device_statuses';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deviceId');
            $table->string('version');
            $table->float('latitude')->index();
            $table->float('longitude')->index();
            $table->timestamp(Status::CREATED_AT)->index();

            $table->foreign('deviceId')->references('id')->on(DevicesMigration::TABLE);
        });
    }
}
