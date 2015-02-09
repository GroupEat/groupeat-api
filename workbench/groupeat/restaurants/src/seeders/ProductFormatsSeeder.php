<?php namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Support\Database\Seeder;

class ProductFormatsSeeder extends Seeder {

    protected function insertAdditionalEntries($id)
    {
        foreach (Product::all() as $product)
        {
            ProductFormat::create([
                'product_id' => $product->id,
                'name' => 'junior',
                'price' => $this->faker->randomFloat(1, 7, 8.9),
            ]);

            ProductFormat::create([
                'product_id' => $product->id,
                'name' => 'sénior',
                'price' => $this->faker->randomFloat(1, 9, 12),
            ]);

            ProductFormat::create([
                'product_id' => $product->id,
                'name' => 'méga',
                'price' => $this->faker->randomFloat(1, 12, 15),
            ]);
        }
    }

}
