<?php

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSilentFailedAndReceivedAtFieldsToNotifications extends Migration
{
    protected $entity = Notification::class;

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->boolean('silent')->default(false)->index();
            $table->boolean('failed')->default(false)->index();
            $table->timestamp(Notification::RECEIVED_AT)->nullable();
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('silent');
            $table->dropColumn('failed');
            $table->dropColumn(Notification::RECEIVED_AT);
        });
    }
}
