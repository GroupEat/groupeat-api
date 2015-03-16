<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateAuthToken;
use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Seeder;

class RestaurantsSeeder extends Seeder
{
    /**
     * @var GenerateAuthToken
     */
    private $generateAuthToken;

    /**
     * @var Category
     */
    private $pizzeriaCategory;

    public function __construct(GenerateAuthToken $generateAuthToken)
    {
        parent::__construct();

        $this->generateAuthToken = $generateAuthToken;
        $this->pizzeriaCategory = Category::findByLabel('pizzeria');
    }

    protected function makeEntry($id, $max)
    {
        $restaurant = Restaurant::create([
            'name' => $this->faker->company,
            'phoneNumber' => $this->faker->phoneNumber,
            'minimumOrderPrice' => $this->faker->numberBetween(1000, 1100),
            'deliveryCapacity' => $this->faker->numberBetween(7, 10),
            'discountPrices' => json_encode([900, 1000, 2000, 2500, 3500, 6000]),
        ]);

        $userCredentials = UserCredentials::create([
            'user' => $restaurant,
            'email' => $this->faker->email,
            'password' => $restaurant->name,
            'locale' => 'fr',
        ]);

        $this->setPizzeriaCategoryFor($restaurant);
        $this->setAuthTokenFor($userCredentials);
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

        foreach ($restaurantsData as $restaurantData) {
            $restaurantData['deliveryCapacity'] = $this->faker->numberBetween(7, 10);
            $restaurantData['minimumOrderPrice'] = 900;
            $restaurantData['discountPrices'] = json_encode([900, 1000, 2000, 2500, 3500, 6000]);

            $restaurant = Restaurant::create($restaurantData);

            $userCredentials = UserCredentials::create([
                'user' => $restaurant,
                'email' => $this->faker->email,
                'password' => $restaurant->name,
                'locale' => 'fr',
            ]);

            $this->setPizzeriaCategoryFor($restaurant);
            $this->setAuthTokenFor($userCredentials);
        }
    }

    private function setPizzeriaCategoryFor(Restaurant $restaurant)
    {
        $restaurant->categories()->sync([$this->pizzeriaCategory->id]);
    }

    private function setAuthTokenFor(UserCredentials $userCredentials)
    {
        $userCredentials->replaceAuthenticationToken($this->generateAuthToken->call($userCredentials));
    }
}
