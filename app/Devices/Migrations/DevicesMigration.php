<?php
namespace Groupeat\Devices\Migrations;

use Groupeat\Customers\Migrations\CustomersMigration;
use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Database\Abstracts\Migration;
use Groupeat\Support\Migrations\Traits\HasLocationMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DevicesMigration extends Migration
{
    use HasLocationMigration;

    protected $entity = Device::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerId');
            $table->string('UUID')->unique();
            $table->string('notificationToken');
            $table->unsignedInteger('platformId');
            $table->string('platformVersion');
            $table->string('model');
            $this->addLocationColumn($table);
            $table->timestamp(Device::CREATED_AT);
            $table->timestamp(Device::UPDATED_AT);
            $table->foreign('customerId')->references('id')->on($this->getTableFor(CustomersMigration::class));
            $table->foreign('platformId')->references('id')->on($this->getTableFor(PlatformsMigration::class));
        });

        $this->addLocationIndex();
    }
}
