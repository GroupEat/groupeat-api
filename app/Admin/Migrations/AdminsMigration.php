<?php
namespace Groupeat\Admin\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminsMigration extends Migration
{
    const TABLE = 'admins';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }
}
