<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class GroupeatMigrateCommand extends Command {

	protected $name = 'groupeat:migrate';
	protected $description = 'Install or reinstall the DB by running all the migrations with the seeds.';

	public function fire()
	{
        $this->setSeed();
        $this->setEntries();

        $this->call('migrate:refresh', ['--seed' => true]);
	}

    protected function setSeed()
    {
        $seedKey = 'database.seed';
        $seed = Config::get($seedKey);

        if ($this->option('random'))
        {
            $seed = false;
            $this->comment('Seeding the DB with random data');
        }
        else if ($this->option('seed'))
        {
            $seed = (int) $this->option('seed');
            $this->comment('Using seed: '.$seed);
        }

        Config::set($seedKey, $seed);
    }

    protected function setEntries()
    {
        $entriesKey = 'database.entries';
        $entries = Config::get($entriesKey);

        if ($this->option('random'))
        {
            $entries = (int) $this->option('entries');
            Config::set($entriesKey, $entries);
        }
    }

	protected function getOptions()
	{
		return [
			['random', 'r', InputOption::VALUE_NONE, 'Use random fake data to seed the DB.', null],
            ['seed', 's', InputOption::VALUE_REQUIRED, 'Specify a seed for the fake data generator.', null],
            ['entries', 'e', InputOption::VALUE_REQUIRED, 'Number of fake entries to seed the DB with.', null],
		];
	}

}
