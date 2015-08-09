<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Seeder;
use Groupeat\Support\Values\PhoneNumber;

class RestaurantsSeeder extends Seeder
{
    /**
     * @var GenerateToken
     */
    private $generateToken;

    /**
     * @var Category
     */
    private $pizzeriaCategory;

    public function __construct(GenerateToken $generateToken)
    {
        parent::__construct();

        $this->generateToken = $generateToken;
        $this->pizzeriaCategory = Category::findByLabel('pizzeria');
    }

    protected function makeEntry($id, $max)
    {
        $restaurant = Restaurant::create([
            'name' => $this->faker->company,
            'phoneNumber' => PhoneNumber::from($this->faker->phoneNumber),
            'minimumOrderPrice' => $this->faker->numberBetween(1000, 1100),
            'deliveryCapacity' => $this->faker->numberBetween(7, 10),
            'discountPrices' => json_encode([900, 1000, 2000, 2500, 3500, 6000]),
            'pictureUrl' => $this->getPictureUrl(),
        ]);

        $userCredentials = UserCredentials::create([
            'user' => $restaurant,
            'email' => $this->faker->email,
            'password' => $restaurant->name,
            'activatedAt' => $restaurant->freshTimestamp(),
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
            [
                'name' => "Toujours ouvert à Paris",
                'phoneNumber' => '0605040303',
            ],
            [
                'name' => "AlloPizza",
                'phoneNumber' => '0605040304',
            ]
        ];

        foreach ($restaurantsData as $restaurantData) {
            $restaurantData['deliveryCapacity'] = $this->faker->numberBetween(7, 10);
            $restaurantData['minimumOrderPrice'] = 900;
            $restaurantData['discountPrices'] = json_encode([900, 1000, 2000, 2500, 3500, 6000]);
            $restaurantData['pictureUrl'] = $this->getPictureUrl();
            $restaurantData['phoneNumber'] = PhoneNumber::from($restaurantData['phoneNumber']);

            $restaurant = Restaurant::create($restaurantData);
            $email = $restaurantData['name'] == 'AlloPizza' ? 'allo@pizza.fr' : $this->faker->email;

            $userCredentials = UserCredentials::create([
                'user' => $restaurant,
                'email' => $email,
                'password' => $restaurant->name,
                'activatedAt' => $restaurant->freshTimestamp(),
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
        $userCredentials->replaceAuthenticationToken($this->generateToken->call($userCredentials));
    }

    private function getPictureUrl()
    {
        $urls = [
            'https://snap-photos.s3.amazonaws.com/img-thumbs/960w/9D0F9026F8.jpg',
            'https://snap-photos.s3.amazonaws.com/img-thumbs/960w/RE54D4GOX0.jpg',
            'https://snap-photos.s3.amazonaws.com/img-thumbs/960w/0HCMIT272C.jpg',
        ];

        return $urls[array_rand($urls)];
    }
}
