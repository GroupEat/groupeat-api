<?php
namespace Groupeat\Support\Console;

use App;
use Config;
use DB;
use File;
use Groupeat\Support\Console\Abstracts\Command;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputOption;

class DbInstall extends Command
{
    protected $name = 'db:install';
    protected $description = "Install the DB by running all the migrations and seed if needed";

    /**
     * @var array The order in which the migrations must be run
     */
    private $order;

    public function fire()
    {
        $this->order = Config::get('database.order');

        if (App::environment('production')) {
            // $this->call('db:backup'); // TODO: Use a similar package for L5 when available
        }

        $this->deleteAllTables();
        $this->createMigrationsTable();
        $this->migrate();

        if ($this->option('seed')) {
            $this->setEntries();
            $this->call('db:seed', ['--force' => $this->option('force')]);
        }
    }

    private function deleteAllTables()
    {
        $tables = array_map(function ($info) {
            $this->comment('Droping table '.$info->table_name);
            DB::statement('DROP TABLE IF EXISTS '.$info->table_name.' CASCADE');
        }, DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'"));
    }

    private function createMigrationsTable()
    {
        if (!Schema::hasTable('migrations')) {
            $this->call('migrate:install');
        }
    }

    private function migrate()
    {
        $this->deleteMigrationFiles();

        foreach (getGroupeatPackagesCollection() as $package) {
            $migrationsDirectory = app_path("$package/Migrations");

            if (is_dir($migrationsDirectory)) {
                array_map(function ($migrationPath) {
                    $this->publishCopy($migrationPath);
                }, File::files($migrationsDirectory));
            }
        }

        $this->call('queue:failed-table');

        system('composer dump-autoload');
        $this->call('migrate:refresh', ['--force' => $this->option('force')]);
    }

    private function deleteMigrationFiles()
    {
        File::delete(File::files(base_path('database/migrations')));
    }

    private function setEntries()
    {
        $entriesKey = 'database.entries';
        $entries = Config::get($entriesKey);

        if ($this->option('entries')) {
            $entries = (int) $this->option('entries');
            Config::set($entriesKey, $entries);
        }

        $this->line("Creating $entries fake entities for each table");
    }

    private function publishCopy($migrationPath)
    {
        $className = trim('Groupeat'.str_replace([app_path(), '/'], ['', '\\'], $migrationPath), '.php');
        $migrationOrder = array_search($className, $this->order);

        if ($migrationOrder === false) {
            throw new \RuntimeException("The order of the migration $className is missing.");
        }

        $baseName = '2015_03_13_'.sprintf('%06d', $migrationOrder).'_'.pathinfo($migrationPath, PATHINFO_BASENAME);
        $dest = base_path("database/migrations/$baseName");

        $migration = preg_replace(
            "/<\?php\s+(namespace[^;]+;)/",
            "<?php\n// Do not edit this file, it is a generated copy of $migrationPath",
            File::get($migrationPath)
        );

        File::put($dest, $migration);
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force the operation to run when in production.', null],
            ['seed', 's', InputOption::VALUE_NONE, 'Migrate and seed.', null],
            ['entries', 'e', InputOption::VALUE_REQUIRED, 'Number of fake entries to seed the DB with.', null],
        ];
    }
}
