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
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('setting_id');
            $table->string('value');

            $table->foreign('customer_id')->references('id')->on(CustomersMigration::TABLE);
            $table->foreign('setting_id')->references('id')->on(SettingsMigration::TABLE);
        });
    }
}
