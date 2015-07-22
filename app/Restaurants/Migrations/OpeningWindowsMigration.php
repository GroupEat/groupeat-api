<?php
namespace Groupeat\Restaurants\Migrations;

use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Migrations\Abstracts\WindowsMigration;
use Illuminate\Database\Schema\Blueprint;

class OpeningWindowsMigration extends WindowsMigration
{
    protected $entity = OpeningWindow::class;

    protected function addFieldsTo(Blueprint $table)
    {
        $table->tinyInteger('dayOfWeek')->index();
        $table->time('start')->index();
        $table->time('end')->index();
    }
}
