<?php

use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class SwitchNotificationTokenToTextType extends Migration
{
    protected $entity = Device::class;

    public function up()
    {
        DB::statement('ALTER TABLE '.$this->getTable().' ALTER COLUMN "notificationToken" TYPE TEXT');
    }

    public function down()
    {
        DB::statement('ALTER TABLE '.$this->getTable().' ALTER COLUMN "notificationToken" TYPE VARCHAR(255)');
    }
}
