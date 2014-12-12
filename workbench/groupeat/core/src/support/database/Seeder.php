<?php namespace Groupeat\Core\Support\Database;

use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Seeder as LaravelSeeder;

abstract class Seeder extends LaravelSeeder {

    use TableGetter;

    protected $faker;
    protected $entries;

    public function __construct()
    {
        $locale = App::getLocale();
        $seed = Config::get('database.seed');

        $fullLocale = strtolower($locale).'_'.strtoupper($locale);
        $this->faker = Factory::create($locale);

        if ($seed)
        {
            $this->faker->seed($seed);
        }

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
    }

    protected function cleanTable()
    {
        DB::table($this->getTable())->delete();
    }
}
