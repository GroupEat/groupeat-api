<?php

use Groupeat\Devices\Entities\DeviceLocation;
use Groupeat\Support\Database\Abstracts\Migration;
use Groupeat\Support\Migrations\Traits\HasLocationMigration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceLocationsTable extends Migration
{
    use HasLocationMigration;

    protected $entity = DeviceLocation::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deviceId');
            $this->addLocationColumn($table);
            $table->timestamp(DeviceLocation::CREATED_AT)->index();

            $table->foreign('deviceId')->references('id')->on($this->getTableFor(CreateDevicesTable::class));
        });

        $this->addLocationIndex();
    }
}
