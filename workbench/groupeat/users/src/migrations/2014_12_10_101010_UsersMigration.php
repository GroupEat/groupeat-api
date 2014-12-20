<?php namespace Groupeat\Users\Migrations;

use Groupeat\Users\Entities\User;
use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UsersMigration extends Migration {

    const TABLE = 'users';

    public function up()
    {
        Schema::create(static::TABLE, function($table)
        {
            $table->increments('id');
            $table->string('email')->unique()->index();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });
    }

}
