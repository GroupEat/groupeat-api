<?php

use Illuminate\Database\Schema\Blueprint;

class OpeningWindowsMigration extends RestaurantWindowsMigration
{
    const TABLE = 'opening_windows';

    protected function addFieldsTo(Blueprint $table)
    {
        $table->tinyInteger('dayOfWeek')->index();
        $table->time('from')->index();
        $table->time('to')->index();
    }
}
