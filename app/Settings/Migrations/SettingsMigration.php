<?php
namespace Groupeat\Settings\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SettingsMigration extends Migration
{
    const TABLE = 'settings';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('cast')->default('string');
            $table->string('label')->unique();
            $table->string('default');
        });
    }
}
