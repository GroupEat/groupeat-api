<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Api\V1\Controller;
use Input;

class RestaurantsController extends Controller {

    public function index()
    {
        $query = Restaurant::with('categories', 'address')->orderBy('name', 'asc');

        if (Input::has('opened'))
        {
            $query->opened();
        }

        if (Input::has('around'))
        {
            $query->around(Input::get('latitude'), Input::get('longitude'));
        }

        return $this->collectionResponse(
            $query->get(),
            new RestaurantTransformer
        );
    }

    public function categoriesIndex()
    {
        return $this->collectionResponse(
            Category::all(),
            new CategoryTransformer
        );
    }

}
