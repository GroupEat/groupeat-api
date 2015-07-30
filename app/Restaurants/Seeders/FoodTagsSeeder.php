<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\FoodTag;
use Groupeat\Support\Database\Abstracts\Seeder;

class FoodTagsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        foreach (['fish', 'meat', 'pepper', 'pork', 'veggie'] as $label) {
            FoodTag::create(compact('label'));
        }
    }
}
