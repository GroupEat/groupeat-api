<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Support\Database\Abstracts\Seeder;

class ProductFormatsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        foreach (Product::all() as $product) {
            ProductFormat::create([
                'productId' => $product->id,
                'name' => 'junior',
                'price' => $this->faker->numberBetween(600, 890),
            ]);

            ProductFormat::create([
                'productId' => $product->id,
                'name' => 'sénior',
                'price' => $this->faker->numberBetween(900, 1200),
            ]);

            ProductFormat::create([
                'productId' => $product->id,
                'name' => 'méga',
                'price' => $this->faker->numberBetween(1200, 1500),
            ]);
        }
    }
}
