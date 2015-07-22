<?php

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    protected $entity = Customer::class;

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('isExternal')->default(false);
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('phoneNumber', 25)->nullable();
            $table->timestamp(Customer::CREATED_AT);
            $table->timestamp(Customer::UPDATED_AT);
            $table->timestamp(Customer::DELETED_AT)->nullable()->index();
        });
    }
}
