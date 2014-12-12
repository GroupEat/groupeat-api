<?php namespace Groupeat\Users\Migrations;

use Groupeat\Users\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Groupeat\Core\Support\Database\Migration;

class CreateUsersTable extends Migration {

    public function getModel()
    {
        return new User;
    }

    public function up()
    {
        Schema::create($this->getTable(), function($table)
        {
            $table->increments('id');
            $table->string('email')->unique()->index();
            $table->string('firstName');
            $table->string('lastName');
            $table->timestamps();
        });
    }

}
