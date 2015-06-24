<?php
namespace Groupeat\Customers\Migrations;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Abstracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomersMigration extends Migration
{
    const TABLE = 'customers';

    public function up()
    {
        Schema::create(static::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('phoneNumber', 25)->nullable();
            $table->timestamp(Customer::CREATED_AT);
            $table->timestamp(Customer::UPDATED_AT);
            $table->timestamp(Customer::DELETED_AT)->nullable()->index();
        });
    }
}
