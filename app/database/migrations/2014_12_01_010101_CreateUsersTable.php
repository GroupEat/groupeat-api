<?php

use Groupeat\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

    const MODEL_CLASS = 'User';

	public function up()
	{
        Schema::create($this->getTableName(), function($table)
        {
            $table->increments('id');
            $table->string('email')->unique()->index();
            $table->string('firstName');
            $table->string('lastName');
            $table->timestamps();
        });
	}

}
