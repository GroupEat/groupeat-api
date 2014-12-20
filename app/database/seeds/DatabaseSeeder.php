<?php

use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Database\SeedersOrderer;

class DatabaseSeeder extends Seeder {

	public function run()
	{
        Entity::$skipValidation = true;
		Eloquent::unguard();

        foreach (SeedersOrderer::getList() as $seeder)
        {
            $this->call($seeder);
        }

        Eloquent::reguard();
        Entity::$skipValidation = false;
	}

}
