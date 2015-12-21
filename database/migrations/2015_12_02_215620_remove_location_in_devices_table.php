<?php

use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Database\Abstracts\Migration;
use Groupeat\Support\Migrations\Traits\HasLocationMigration;
use Illuminate\Database\Schema\Blueprint;

class RemoveLocationInDevicesTable extends Migration
{
    use HasLocationMigration;

    protected $entity = Device::class;

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $this->dropLocationIndex($table);
        });

        Schema::table($this->getTable(), function (Blueprint $table) {
            $this->dropLocationColumn($table);
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $this->addLocationColumn($table)->nullable();
        });

        $this->addLocationIndex();
    }
}
