<?php
namespace Groupeat\Support\Database\Abstracts;

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
        $locale = 'fr';
        $this->faker = Factory::create(strtolower($locale).'_'.strtoupper($locale));
        $this->entries = (int) Config::get('database.entries');
    }

    public function run()
    {
        $id = 1;

        if (method_exists($this, 'makeEntry')) {
            for ($id = 1; $id <= $this->entries; $id++) {
                $this->makeEntry($id, $this->entries);
            }
        }

        if (method_exists($this, 'insertAdditionalEntries')) {
            $this->insertAdditionalEntries($id);
        }
    }
}
