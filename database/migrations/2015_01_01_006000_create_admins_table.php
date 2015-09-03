<?php

use Groupeat\Admin\Entities\Admin;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    protected $entity = Admin::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->timestamp(Admin::CREATED_AT);
            $table->timestamp(Admin::UPDATED_AT);
            $table->timestamp(Admin::DELETED_AT)->nullable()->index();
        });
    }
}
