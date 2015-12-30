<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Abstracts\Seeder;
use Groupeat\Support\Database\Traits\GeneratePhoneNumber;
use Illuminate\Support\Arr;

class RestaurantsSeeder extends Seeder
{
    use GeneratePhoneNumber;

    private $generateToken;
    private $pizzeriaCategory;
    private $discountPolicy = [
        900 => 0,
        1000 => 10,
        2000 => 20,
        2500 => 30,
        3500 => 40,
        6000 => 50,
    ];

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
            'rating' => $this->faker->randomDigitNotNull(),
            'phoneNumber' => $this->generatePhoneNumber(),
            'minimumGroupOrderPrice' => $this->faker->numberBetween(1000, 1100),
            'deliveryCapacity' => $this->faker->numberBetween(7, 10),
            'discountPolicy' => $this->discountPolicy,
            'pictureUrl' => $this->getPictureUrl(),
        ]);

        $credentials = new UserCredentials([
            'email' => $this->faker->email,
            'password' => $restaurant->name,
            'activatedAt' => $restaurant->freshTimestamp(),
            'locale' => 'fr',
        ]);
        $credentials->user()->associate($restaurant);
        $credentials->save();

        $this->setPizzeriaCategoryFor($restaurant);
        $this->setAuthTokenFor($credentials);
    }

    protected function insertAdditionalEntries($id)
    {
        $restaurantsData = [
            [
                'name' => "Pizza Di Genova",
                'email' => 'pizza@genova.fr',
                'phoneNumber' => '0605040302',
            ],
            [
                'name' => "Toujours ouvert",
                'email' => 'toujours@ouvert.fr',
                'phoneNumber' => '0605040301',
            ],
            [
                'name' => "Toujours fermé",
                'email' => 'toujours@ferme.fr',
                'phoneNumber' => '0605040300',
            ],
            [
                'name' => "Toujours ouvert à Paris",
                'email' => 'ouvert@paris.fr',
                'phoneNumber' => '0605040303',
            ],
            [
                'name' => "AlloPizza",
                'email' => 'allo@pizza.fr',
                'phoneNumber' => '0605040304',
            ]
        ];

        foreach ($restaurantsData as $restaurantData) {
            $restaurantData['rating'] = $this->faker->randomDigitNotNull();
            $restaurantData['deliveryCapacity'] = $this->faker->numberBetween(7, 10);
            $restaurantData['minimumGroupOrderPrice'] = 900;
            $restaurantData['discountPolicy'] = $this->discountPolicy;
            $restaurantData['pictureUrl'] = $this->getPictureUrl();
            $restaurantData['phoneNumber'] = $this->generatePhoneNumber();

            $restaurant = Restaurant::create(Arr::except($restaurantData, 'email'));

            $credentials = new UserCredentials([
                'email' => $restaurantData['email'],
                'password' => 'groupeat',
                'activatedAt' => $restaurant->freshTimestamp(),
                'locale' => 'fr',
            ]);
            $credentials->user()->associate($restaurant);
            $credentials->save();

            $this->setPizzeriaCategoryFor($restaurant);
            $this->setAuthTokenFor($credentials);
        }
    }

    private function setPizzeriaCategoryFor(Restaurant $restaurant)
    {
        $restaurant->categories()->sync([$this->pizzeriaCategory->id]);
    }

    private function setAuthTokenFor(UserCredentials $credentials)
    {
        $credentials->replaceAuthenticationToken($this->generateToken->call($credentials));
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
