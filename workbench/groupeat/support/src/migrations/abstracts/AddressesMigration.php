<?php
namespace Groupeat\Support\Migrations\Abstracts;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class AddressesMigration extends Migration
{
    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $this->addFields($table);
            $table->string('street');
            $table->string('details')->nullable();
            $table->string('city');
            $table->string('postcode');
            $table->string('state');
            $table->string('country');
            $table->float('latitude')->index();
            $table->float('longitude')->index();
        });
    }

    protected function addFields(Blueprint $table)
    {
        // Implement by inheritance if needed
    }
}
