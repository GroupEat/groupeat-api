<?php

use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Migrations\Abstracts\CreateWindowsTable;
use Illuminate\Database\Schema\Blueprint;

class CreateOpeningWindowsTable extends CreateWindowsTable
{
    protected $entity = OpeningWindow::class;

    protected function addFieldsTo(Blueprint $table)
    {
        $table->tinyInteger('dayOfWeek')->index();
        $table->time('start')->index();
        $table->time('end')->index();
    }
}
