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
            $table->unsignedInteger('customerId');
            $table->string('UUID')->unique();
            $table->string('notificationToken');
            $table->unsignedInteger('platformId');
            $table->string('version');
            $table->string('model');
            $table->float('latitude')->index();
            $table->float('longitude')->index();
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');

            $table->foreign('customerId')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('platformId')->references('id')->on(PlatformsMigration::TABLE);
        });
    }
}
