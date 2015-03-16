<?php
namespace Groupeat\Support\Database\Abstracts;

use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Illuminate\Support\Facades\Schema;

abstract class Migration extends LaravelMigration
{
    const TABLE = 'Defined by inheritance';

    /**
     * Run the migrations.
     */
    public function up()
    {
        // Implemented by inheritance
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(static::TABLE);
    }
}
