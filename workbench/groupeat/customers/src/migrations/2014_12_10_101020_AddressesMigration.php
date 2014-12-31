<?php namespace Groupeat\Customers\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddressesMigration extends Migration {

    const TABLE = 'customer_adresses';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->index();
            $table->string('street');
            $table->string('details');
            $table->string('city');
            $table->string('postcode');
            $table->string('state');
            $table->string('country');
            $table->float('longitude');
            $table->float('latitude');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

}
