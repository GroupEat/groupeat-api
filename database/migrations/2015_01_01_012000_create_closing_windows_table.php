<?php

use Groupeat\Restaurants\Entities\ClosingWindow;
use Groupeat\Restaurants\Migrations\Abstracts\CreateWindowsTable;
use Illuminate\Database\Schema\Blueprint;

class CreateClosingWindowsTable extends CreateWindowsTable
{
    protected $entity = ClosingWindow::class;

    protected function addFieldsTo(Blueprint $table)
    {
        $table->timestamp('start')->index();
        $table->timestamp('end')->index();
    }
}
