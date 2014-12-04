<?php namespace Groupeat\Database;

use App;
use Config;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder as LaravelSeeder;

abstract class Seeder extends LaravelSeeder {

    use TableNameGetter;

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
        DB::table($this->getTableName())->delete();
    }

}
