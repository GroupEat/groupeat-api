<?php namespace Groupeat\Support\Database;

use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Illuminate\Support\Facades\Schema;

abstract class Migration extends LaravelMigration {

    const TABLE = 'Defined by inheritance';


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Implemented by inheritance
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(static::TABLE);
    }

}
