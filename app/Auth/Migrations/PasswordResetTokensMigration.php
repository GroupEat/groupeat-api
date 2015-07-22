<?php
namespace Groupeat\Auth\Migrations;

use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PasswordResetTokensMigration extends Migration
{
    protected $table = 'password_reset_tokens';

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->string('email')->unique();
            $table->string('token')->unique();
            $table->timestamp(Model::CREATED_AT);
        });
    }
}
