<?php
namespace Groupeat\Settings\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerSettingMigration extends Migration
{
    const TABLE = 'customer_setting';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerId');
            $table->unsignedInteger('settingId');
            $table->string('value');

            $table->foreign('customerId')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('settingId')->references('id')->on(SettingsMigration::TABLE);
        });
    }
}
