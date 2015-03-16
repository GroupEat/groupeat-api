<?php
namespace Groupeat\Auth\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserCredentialsMigration extends Migration
{
    const TABLE = 'user_credentials';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->morphs('user');
            $table->timestamp('activated_at')->nullable();
            $table->string('activationToken')->nullable()->unique();
            $table->string('password');
            $table->text('token')->unique()->nullable();
            $table->string('locale', 6);
        });
    }
}
