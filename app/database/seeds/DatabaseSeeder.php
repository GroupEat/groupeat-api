<?php

use Groupeat\Core\Support\Database\SeedersOrderer;

class DatabaseSeeder extends Seeder {

	public function run()
	{
		Eloquent::unguard();

        foreach (SeedersOrderer::getList() as $seeder)
        {
            $this->call($seeder);
        }
	}

}
