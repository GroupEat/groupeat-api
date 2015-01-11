<?php namespace Groupeat\Support\Console;

use Config;
use DB;
use File;
use Schema;
use Symfony\Component\Console\Input\InputOption;

class DbInstallCommand extends Command {

	protected $name = 'db-install';
	protected $description = "Install the DB by running all the migrations and seed if needed";


    public function fire()
	{
        $this->createMigrationsTable();
        $this->migrate();

        if ($this->option('with-seeds'))
        {
            $this->setSeed();
            $this->setEntries();
            $this->call('db:seed', $this->getDbOptions());
        }
	}

    private function createMigrationsTable()
    {
        if (!Schema::connection($this->option('database'))->hasTable('migrations'))
        {
            $this->call('migrate:install', ['--database' => $this->option('database')]);
        }
    }

    private function migrate()
    {
        $this->deleteOldMigrationFiles();

        foreach (listGroupeatPackagesWithoutSupport() as $package)
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
        $this->call('migrate:refresh', $this->getDbOptions());
    }

    private function deleteOldMigrationFiles()
    {
        File::delete(File::files(app_path('database/migrations')));
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
            $this->comment("Using seed: $seed");
        }
        else
        {
            return;
        }

        Config::set('database.seed', $seed);
    }

    private function setEntries()
    {
        $entriesKey = 'database.entries';
        $entries = Config::get($entriesKey);

        if ($this->option('entries'))
        {
            $entries = (int) $this->option('entries');
            Config::set($entriesKey, $entries);
        }

        $this->line("Creating $entries fake entities for each table");
    }

    private function publishCopy($migrationPath)
    {
        $migration = File::get($migrationPath);
        $lines = explode("\n", $migration);
        array_shift($lines);
        array_unshift($lines, "// Do not edit this file, it is a generated copy of $migrationPath");
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
            ['database', 'd', InputOption::VALUE_REQUIRED, 'The database connection to use.', null],
        ];
    }

    private function getDbOptions()
    {
        return [
            '--force' => $this->option('force'),
            '--database' => $this->option('database'),
        ];
    }

}
