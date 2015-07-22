<?php
namespace Groupeat\Auth\Migrations;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserCredentialsMigration extends Migration
{
    protected $entity = UserCredentials::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->unsignedInteger("userId");
            $table->string("userType");
            $table->timestamp(UserCredentials::ACTIVATED_AT)->nullable();
            $table->string('activationToken')->nullable()->unique();
            $table->string('password');
            $table->text('token')->unique()->nullable();
            $table->string('locale', 6);

            $table->index(['userType', 'userId']);
        });
    }
}
