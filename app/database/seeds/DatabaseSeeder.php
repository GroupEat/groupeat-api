<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

        // The seeder need to be run in a good chronological order to avoid foreign key problem
        $orderedSeeders = ['Users'];

        foreach ($orderedSeeders as $seeder)
        {
            $this->call($seeder . 'Seeder');
        }
	}

}
