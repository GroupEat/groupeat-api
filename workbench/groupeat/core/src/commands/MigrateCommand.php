<?php namespace Groupeat\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends Command {

	protected $name = 'groupeat:migrate';
	protected $description = 'Install or reinstall the DB by running all the migrations';

    public function fire()
	{
        $this->createMigrationsTable();
        $this->migrate();

        if ($this->option('with-seeds'))
        {
            $this->setSeed();
            $this->setEntries();
            $this->call('db:seed', ['--force' => $this->option('force')]);
        }
	}

    private function createMigrationsTable()
    {
        if (!Schema::hasTable('migrations'))
        {
            $this->call('migrate:install');
        }
    }

    private function migrate()
    {
        foreach (listGroupeatPackagesWithoutCore() as $package)
        {
            $migrationsDirectory = workbench_path($package, 'migrations');

            if (File::isDirectory($migrationsDirectory))
            {
                array_map(function($migrationPath)
                {
                    $this->publishCopy($migrationPath);
                }, File::files($migrationsDirectory));
            }
        }

        processAtProjectRoot('composer dump-autoload', $this->output);
        $this->call('migrate:refresh', ['--force' => $this->option('force')]);
    }

    private function setSeed()
    {
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
        else
        {
            // Use the configured seed
            return;
        }

        Config::set('database.seed', $seed);
    }

    private function setEntries()
    {
        $entriesKey = 'database.entries';
        $entries = Config::get($entriesKey);

        if ($this->option('random'))
        {
            $entries = (int) $this->option('entries');
            Config::set($entriesKey, $entries);
        }
    }

    private function publishCopy($migrationPath)
    {
        $migration = File::get($migrationPath);
        $lines = explode("\n", $migration);
        array_shift($lines);
        array_unshift($lines, "// Do not edit this file, it is a copy of ".$migrationPath);
        array_unshift($lines, "<?php");
        $dest = app_path('database/migrations/'.pathinfo($migrationPath, PATHINFO_BASENAME));
        File::put($dest, implode("\n", $lines));
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force the operation to run when in production.', null],
            ['with-seeds', 'w', InputOption::VALUE_NONE, 'Migrate and seed.', null],
            ['random', 'r', InputOption::VALUE_NONE, 'Use random fake data to seed the DB.', null],
            ['seed', 's', InputOption::VALUE_REQUIRED, 'Specify a seed for the fake data generator.', null],
            ['entries', 'e', InputOption::VALUE_REQUIRED, 'Number of fake entries to seed the DB with.', null],
        ];
    }

}