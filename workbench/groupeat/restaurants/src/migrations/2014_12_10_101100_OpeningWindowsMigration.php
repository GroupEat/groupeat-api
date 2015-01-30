<?php namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Migrations\Abstracts\WindowsMigration;
use Illuminate\Database\Schema\Blueprint;

class OpeningWindowsMigration extends WindowsMigration {

    const TABLE = 'opening_windows';


    protected function addFieldsTo(Blueprint $table)
    {
        $table->tinyInteger('dayOfWeek')->index();
        $table->time('from')->index();
        $table->time('to')->index();
    }

}
