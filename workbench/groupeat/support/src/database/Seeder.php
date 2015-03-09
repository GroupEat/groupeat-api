<?php
namespace Groupeat\Support\Database;

use Config;
use DB;
use Faker\Factory;
use Illuminate\Database\Seeder as LaravelSeeder;

abstract class Seeder extends LaravelSeeder
{
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
        $locale = Config::get('app.available_frontend_locales')[0];
        $this->faker = Factory::create(strtolower($locale).'_'.strtoupper($locale));
        $this->entries = (int) Config::get('database.entries');
    }

    public function run()
    {
        $id = 1;
        $this->cleanTable();

        if (method_exists($this, 'makeEntry')) {
            for ($id = 1; $id <= $this->entries; $id++) {
                $this->makeEntry($id, $this->entries);
            }
        }

        if (method_exists($this, 'insertAdditionalEntries')) {
            $this->insertAdditionalEntries($id);
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
        $migrationClass = str_replace('Seeder', 'Migration', static::class);

        return new $migrationClass();
    }

    protected function cleanTable()
    {
        DB::table($this->getTable())->delete();
    }
}
