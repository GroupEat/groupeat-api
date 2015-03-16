<?php

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Database\SeedersOrderer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $seedersOrderer;

    public function __construct(SeedersOrderer $seedersOrderer)
    {
        $this->seedersOrderer = $seedersOrderer;
    }

    public function run()
    {
        Entity::$skipValidation = true;
        Eloquent::unguard();

        foreach ($this->seedersOrderer->getList() as $seeder) {
            $this->call($seeder);
        }

        Eloquent::reguard();
        Entity::$skipValidation = false;
    }
}
