<?php
namespace Groupeat\Devices\Migrations;

use Groupeat\Devices\Entities\Platform;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlatformsMigration extends Migration
{
    protected $entity = Platform::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
        });
    }
}
