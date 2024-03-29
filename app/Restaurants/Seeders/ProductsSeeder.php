<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Seeder;

class ProductsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        $pizzaType = FoodType::findByLabel('pizza');

        foreach (Restaurant::all() as $restaurant) {
            $products = [
                [
                    'name' => 'margherita',
                    'description' => "Mozzarella, basilic frais et tomates.",
                ],
                [
                    'name' => 'paysanne',
                    'description' => "Tomate, mozzarella, poitrine fumée et œuf.",
                ],
                [
                    'name' => 'classica',
                    'description' => "Tomate, mozzarella et origan.",
                ],
                [
                    'name' => 'napolitaine',
                    'description' => "Tomate, mozzarella, anchois, câpres et olives.",
                ],
            ];

            foreach ($products as $product) {
                Product::create([
                    'restaurantId' => $restaurant->id,
                    'typeId' => $pizzaType->id,
                    'name' => $product['name'],
                    'description' => $product['description'],
                ]);
            }
        }
    }
}
