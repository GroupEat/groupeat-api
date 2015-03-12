<?php

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class PasswordResetTokensMigration extends Migration
{
    const TABLE = 'password_reset_tokens';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->string('email')->unique();
            $table->string('token')->unique();
            $table->timestamp('created_at');
        });
    }
}
