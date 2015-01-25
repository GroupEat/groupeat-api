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
