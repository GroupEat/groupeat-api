<?php namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Migrations\Abstracts\WindowsMigration;
use Illuminate\Database\Schema\Blueprint;

class ClosingWindowsMigration extends WindowsMigration {

    const TABLE = 'closing_windows';


    protected function addFieldsTo(Blueprint $table)
    {
        $table->timestamp('from')->index();
        $table->timestamp('to')->index();
    }

}
