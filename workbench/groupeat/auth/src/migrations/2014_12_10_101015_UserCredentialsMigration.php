<?php namespace Groupeat\Auth\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserCredentialsMigration extends Migration {

    const TABLE = 'user_credentials';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('email')->unique()->index();
            $table->morphs('user');
            $table->timestamp('activated_at')->nullable()->index();
            $table->string('activationToken')->nullable()->unique()->index();
            $table->string('password')->nullable()->index();
            $table->string('token')->unique()->nullable();
            $table->string('locale', 6);
        });
    }

}
