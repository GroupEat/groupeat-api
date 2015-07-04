<?php
namespace Groupeat\Support\Migrations\Traits;

use Illuminate\Database\Schema\Blueprint;

trait HasLocationMigration
{
    protected function addLocationColumn(Blueprint $table)
    {
        $table->point('location');
    }

    protected function addLocationIndex()
    {
        \DB::statement('CREATE INDEX '.static::TABLE.'_location_gist ON '.static::TABLE.' USING GIST(location)');
    }
}
