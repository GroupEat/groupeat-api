<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\Category;
use Groupeat\Support\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        foreach (['pizzeria', 'japanese', 'chinese'] as $label) {
            Category::create(compact('label'));
        }
    }
}
