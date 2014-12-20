<?php namespace Groupeat\Users\Migrations;

use Groupeat\Users\Entities\User;
use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddressesMigration extends Migration {

    const TABLE = 'user_adresses';

    public function up()
    {
        Schema::create(static::TABLE, function($table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('street');
            $table->string('details');
            $table->string('city');
            $table->string('postcode');
            $table->string('state');
            $table->string('country');
            $table->float('longitude');
            $table->float('latitude');

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

}
