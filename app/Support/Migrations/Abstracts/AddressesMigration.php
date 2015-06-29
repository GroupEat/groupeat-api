<?php
namespace Groupeat\Support\Migrations\Abstracts;

use Groupeat\Support\Database\Abstracts\Migration;
use Groupeat\Support\Migrations\Traits\HasPositionMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class AddressesMigration extends Migration
{
    use HasPositionMigration;

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
            $this->addPositionFields($table);
        });
    }

    protected function addFields(Blueprint $table)
    {
        // Implement by inheritance if needed
    }
}
