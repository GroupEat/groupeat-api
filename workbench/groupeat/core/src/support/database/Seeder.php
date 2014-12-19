<?php namespace Groupeat\Core\Support\Database;

use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Seeder as LaravelSeeder;

abstract class Seeder extends LaravelSeeder {

    use TableGetter;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var int number of entities to create
     */
    protected $entries;


    public function __construct()
    {
        $this->faker = $this->makeFaker(App::getLocale(), Config::get('database.seed'));
        $this->entries = (int) Config::get('database.entries');
    }

    public function run()
    {
        $this->cleanTable();

        if (method_exists($this, 'makeEntry'))
        {
            for ($i = 0; $i < $this->entries; $i++)
            {
                $this->makeEntry();
            }
        }

        if (method_exists($this, 'insertAdditionalEntries'))
        {
            $this->insertAdditionalEntries();
        }
    }

    protected function cleanTable()
    {
        DB::table($this->getTable())->delete();
    }

    protected function makeFaker($locale, $seed)
    {
        $fullLocale = strtolower($locale).'_'.strtoupper($locale);

        $faker = Factory::create($fullLocale);

        if ($seed)
        {
            $faker->seed($seed);
        }

        return $faker;
    }
}
