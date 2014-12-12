<?php namespace Groupeat\Core\Support\Database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration as LaravelMigration;

abstract class Migration extends LaravelMigration {

    use TableGetter;

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
        Schema::dropIfExists($this->getTable());
    }

}
