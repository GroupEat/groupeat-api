<?php
namespace Groupeat\Restaurants\Http\V1;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use League\Period\Period;

class RestaurantsController extends Controller
{
    public function index()
    {
        $query = Restaurant::orderBy('name', 'asc');

        if ((bool) $this->get('opened')) {
            $query->opened();
        }

        if ((bool) $this->get('around')) {
            $query->around($this->get('latitude'), $this->get('longitude'));
        }

        return $this->collectionResponse($query->get(), new RestaurantTransformer());
    }

    public function categoriesIndex()
    {
        return $this->collectionResponse(
            Category::all(),
            new CategoryTransformer
        );
    }

    public function foodTypesIndex()
    {
        return $this->collectionResponse(
            FoodType::all(),
            new FoodTypeTransformer
        );
    }

    public function productsIndex(Restaurant $restaurant)
    {
        return $this->collectionResponse(
            $restaurant->products,
            new ProductTransformer()
        );
    }

    public function productFormatsIndex(Product $product)
    {
        return $this->collectionResponse(
            $product->formats,
            new ProductFormatTransformer()
        );
    }

    public function show(Restaurant $restaurant)
    {
        $start = Carbon::now()->hour(23)->minute(45);
        $end = $start->copy()->addMinutes(45);

        return (string) $restaurant->isOpened(new Period($start, $end));

        //return $this->itemResponse($restaurant);
    }

    public function showAddress(Restaurant $restaurant)
    {
        return $this->itemResponse($restaurant->address);
    }
}
