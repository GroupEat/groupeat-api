<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Api\V1\Controller;

class RestaurantsController extends Controller {

    public function index()
    {
        return $this->collectionResponse(Restaurant::with('foodTypes', 'address')->get(), new RestaurantTransformer);
    }

    public function showAddress(Restaurant $restaurant)
    {
        return $this->itemResponse($restaurant->address);
    }

    public function foodTypesIndex()
    {
        return $this->collectionResponse(FoodType::all(), new FoodTypeTransformer);
    }

}
