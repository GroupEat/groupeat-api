<?php
namespace Groupeat\Devices\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OperatingSystemsMigration extends Migration
{
    const TABLE = 'operating_systems';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
        });
    }
}
