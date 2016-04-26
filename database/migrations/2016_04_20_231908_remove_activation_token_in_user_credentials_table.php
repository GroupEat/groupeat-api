<?php

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveActivationTokenInUserCredentialsTable extends Migration
{
    protected $entity = UserCredentials::class;

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('activationToken');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('activationToken')->nullable()->unique();
        });
    }
}
