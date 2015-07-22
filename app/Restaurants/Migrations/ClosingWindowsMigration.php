<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Entities\ClosingWindow;
use Groupeat\Restaurants\Migrations\Abstracts\WindowsMigration;
use Illuminate\Database\Schema\Blueprint;

class ClosingWindowsMigration extends WindowsMigration
{
    protected $entity = ClosingWindow::class;

    protected function addFieldsTo(Blueprint $table)
    {
        $table->timestamp('start')->index();
        $table->timestamp('end')->index();
    }
}
