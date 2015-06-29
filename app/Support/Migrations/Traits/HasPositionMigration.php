<?php
namespace Groupeat\Support\Migrations\Traits;

use Illuminate\Database\Schema\Blueprint;

trait HasPositionMigration
{
    protected function addPositionFields(Blueprint $table)
    {
        $table->float('latitude')->index();
        $table->float('longitude')->index();
    }
}
