<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Support\Database\Seeder;

class FoodTypesSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        foreach (['pizza', 'kebab', 'salad'] as $label) {
            FoodType::create(compact('label'));
        }
    }
}
