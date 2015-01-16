<?php namespace Groupeat\Auth\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PasswordResetTokensMigration extends Migration {

    const TABLE = 'password_reset_tokens';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->string('email')->index();
            $table->string('token')->unique()->index();
            $table->timestamp('created_at');
        });
    }

}
