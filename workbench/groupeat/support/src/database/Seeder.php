<?php namespace Groupeat\Support\Database;

use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Seeder as LaravelSeeder;

abstract class Seeder extends LaravelSeeder {

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var int Number of fake entities to create
     */
    protected $entries;


    public function __construct()
    {
        $this->faker = $this->makeFaker('fr_FR', Config::get('database.seed'));
        $this->entries = (int) Config::get('database.entries');
    }

    public function run()
    {
        $this->cleanTable();

        if (method_exists($this, 'makeEntry'))
        {
            for ($i = 0; $i < $this->entries; $i++)
            {
                $this->makeEntry($i, $this->entries);
            }
        }

        if (method_exists($this, 'insertAdditionalEntries'))
        {
            $this->insertAdditionalEntries();
        }
    }

    protected function getTable()
    {
        $migration = $this->getRelatedMigration();

        return $migration::TABLE;
    }

    /**
     * @return \Groupeat\Support\Database\Migration
     */
    protected function getRelatedMigration()
    {
        $migrationClass = str_replace('Seeder', 'Migration', get_class($this));

        return new $migrationClass;
    }

    protected function cleanTable()
    {
        DB::table($this->getTable())->delete();
    }

    protected function makeFaker($locale, $seed)
    {
        $faker = Factory::create($locale);

        if ($seed)
        {
            $faker->seed($seed);
        }

        return $faker;
    }
}
