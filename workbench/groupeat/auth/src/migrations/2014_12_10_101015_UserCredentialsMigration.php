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
            $table->string('password');
            $table->string('activationCode')->nullable();
            $table->timestamp('activated_at')->nullable()->index();
            $table->timestamp('last_login')->nullable()->index();
            $table->morphs('user');
            $table->timestamps();
            $table->softDeletes();
        });
    }

}
