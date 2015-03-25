<?php
namespace Groupeat\Devices\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlatformsMigration extends Migration
{
    const TABLE = 'platforms';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
        });
    }
}
