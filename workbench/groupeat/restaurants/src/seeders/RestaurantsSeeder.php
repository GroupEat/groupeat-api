<?php namespace Groupeat\Restaurants\Seeders;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Database\Seeder;

class RestaurantsSeeder extends Seeder {

    /**
     * @var FoodType
     */
    private $pizzaType;


    public function __construct()
    {
        parent::__construct();

        $this->pizzaType = FoodType::findByType('pizza');
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

        $this->setPizzaTypeFor($restaurant);
    }

    protected function insertAdditionalEntries($id)
    {
        $restaurant = Restaurant::create([
            'name' => 'Pizza Di Genova',
            'phoneNumber' => '0605040302',
        ]);

        UserCredentials::create([
            'user' => $restaurant,
            'email' => $this->faker->email,
            'password' => $restaurant->name,
            'locale' => 'fr',
        ]);

        $this->setPizzaTypeFor($restaurant);
    }

    private function setPizzaTypeFor(Restaurant $restaurant)
    {
        $restaurant->foodTypes()->sync([$this->pizzaType->id]);
    }

}
