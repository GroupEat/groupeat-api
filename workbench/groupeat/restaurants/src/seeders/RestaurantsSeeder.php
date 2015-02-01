<?php namespace Groupeat\Restaurants\Seeders;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Seeder;

class RestaurantsSeeder extends Seeder {

    /**
     * @var Category
     */
    private $pizzeriaCategory;


    public function __construct()
    {
        parent::__construct();

        $this->pizzeriaCategory = Category::findByLabel('pizzeria');
    }

    protected function makeEntry($id, $max)
    {
        $restaurant = Restaurant::create([
            'name' => $this->faker->company,
            'phoneNumber' => $this->faker->phoneNumber,
            'minimumOrderPrice' => $this->faker->numberBetween(10, 11),
            'deliveryCapacity' => $this->faker->numberBetween(5, 10),
            'reductionPrices' => json_encode([9, 10, 20, 25, 35, 60]),
        ]);

        UserCredentials::create([
            'user' => $restaurant,
            'email' => $this->faker->email,
            'password' => $restaurant->name,
            'locale' => 'fr',
        ]);

        $this->setPizzeriaCategoryFor($restaurant);
    }

    protected function insertAdditionalEntries($id)
    {
        $restaurantsData = [
            [
                'name' => "Pizza Di Genova",
                'phoneNumber' => '0605040302',
            ],
            [
                'name' => "Toujours ouvert",
                'phoneNumber' => '0605040301',
            ],
            [
                'name' => "Toujours fermé",
                'phoneNumber' => '0605040300',
            ],
        ];

        foreach ($restaurantsData as $restaurantData)
        {
            $restaurantData['deliveryCapacity'] = $this->faker->numberBetween(5, 10);
            $restaurantData['minimumOrderPrice'] = $this->faker->numberBetween(10, 11);
            $restaurantData['reductionPrices'] = json_encode([9, 10, 20, 25, 35, 60]);

            $restaurant = Restaurant::create($restaurantData);

            UserCredentials::create([
                'user' => $restaurant,
                'email' => $this->faker->email,
                'password' => $restaurant->name,
                'locale' => 'fr',
            ]);

            $this->setPizzeriaCategoryFor($restaurant);
        }
    }

    private function setPizzeriaCategoryFor(Restaurant $restaurant)
    {
        $restaurant->categories()->sync([$this->pizzeriaCategory->id]);
    }

}
