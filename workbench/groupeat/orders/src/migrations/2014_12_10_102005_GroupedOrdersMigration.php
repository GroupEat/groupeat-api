<?php namespace Groupeat\Orders\Migrations;

use Groupeat\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GroupedOrdersMigration extends Migration {

    const TABLE = 'grouped_orders';


    public function up()
    {
        Schema::create(static::TABLE, function(Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->index(['created_at', 'updated_at']);
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamp('ending_at')->index();
            $table->timestamp('confirmed_at')->nullable()->index();
        });
    }

}
