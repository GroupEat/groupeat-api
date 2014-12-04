<?php namespace Groupeat\Database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration as LaravelMigration;

abstract class Migration extends LaravelMigration {

    use TableNameGetter;

    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // Implemented in daugther classes
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists($this->getTableName());
	}

}
