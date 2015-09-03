<?php

use Groupeat\Settings\Entities\CustomerSettings;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSettingsTable extends Migration
{
    protected $entity = CustomerSettings::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerId');
            $table->boolean(CustomerSettings::NOTIFICATIONS_ENABLED)->index();
            $table->unsignedInteger(CustomerSettings::DAYS_WITHOUT_NOTIFYING)->index();
            $table->time(CustomerSettings::NO_NOTIFICATION_AFTER)->index();

            $table->foreign('customerId')->references('id')->on($this->getTableFor(CreateCustomersTable::class));
        });
    }
}
