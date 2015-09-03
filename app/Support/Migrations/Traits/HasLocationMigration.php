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
        \DB::statement('CREATE INDEX '.$this->getTable().'_location_gist ON '.$this->getTable().' USING GIST(location)');
    }
}
