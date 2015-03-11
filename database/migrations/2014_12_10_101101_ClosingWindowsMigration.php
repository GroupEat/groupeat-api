<?php

use Illuminate\Database\Schema\Blueprint;

class ClosingWindowsMigration extends RestaurantWindowsMigration
{
    const TABLE = 'closing_windows';

    protected function addFieldsTo(Blueprint $table)
    {
        $table->timestamp('from')->index();
        $table->timestamp('to')->index();
    }
}
