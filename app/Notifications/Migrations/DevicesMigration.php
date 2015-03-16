<?php
namespace Groupeat\Notifications\Migrations;

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
            $table->string('device_id');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on(CustomersMigration::TABLE);
        });
    }
}