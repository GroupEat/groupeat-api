<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\FoodTag;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Support\Database\Abstracts\Seeder;

class FoodTagProductSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        $foodTagsCount = FoodTag::all()->count();

        Product::all()->each(function (Product $product) use ($foodTagsCount) {
            $possibleIds = range(1, $foodTagsCount);
            shuffle($possibleIds);
            $ids = array_slice($possibleIds, rand(1, $foodTagsCount - 1));
            $product->tags()->sync($ids);
        });
    }
}
