<?php
namespace Groupeat\Support\Migrations\Traits;

use DB;
use Illuminate\Database\Schema\Blueprint;

trait HasLocationMigration
{
    protected function addLocationColumn(Blueprint $table)
    {
        return $table->point('location');
    }

    protected function dropLocationColumn(Blueprint $table)
    {
        $table->dropColumn('location');
    }

    protected function addLocationIndex()
    {
        DB::statement('CREATE INDEX '.$this->getLocationIndexName().' ON '.$this->getTable().' USING GIST(location)');
    }

    protected function dropLocationIndex(Blueprint $table)
    {
        $table->dropIndex($this->getLocationIndexName());
    }

    private function getLocationIndexName()
    {
        return $this->getTable().'_location_gist';
    }
}
