<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Support\Database\Abstracts\Seeder;

class FoodTypesSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        foreach (['pizza', 'maki', 'sushi', 'sashimi', 'fugou', 'chirachi'] as $label) {
            FoodType::create(compact('label'));
        }
    }
}
