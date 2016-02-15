<?php
namespace Groupeat\Restaurants\Http\V1;

use Carbon\Carbon;
use Groupeat\Orders\Values\MaximumOrderFlowInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use League\Period\Period;
use Phaza\LaravelPostgis\Geometries\Point;

class RestaurantsController extends Controller
{
    public function index(
        MaximumOrderFlowInMinutes $maximumOrderFlowInMinutes,
        MinimumFoodrushInMinutes $minimumFoodrushInMinutes
    ) {
        $query = Restaurant::orderBy('name', 'asc');

        if ((bool) $this->get('opened')) {
            $start = Carbon::now()->addMinutes($minimumFoodrushInMinutes->value());
            $end = $start->copy()->addMinutes($maximumOrderFlowInMinutes->value());
            $query->opened(new Period($start, $end));
        }

        if ((bool) $this->get('around')) {
            $query->around(new Point($this->get('latitude'), $this->get('longitude')));
        }

        $restaurants = $query->get()->load('openingWindows', 'closingWindows', 'credentials');

        return $this->collectionResponse($restaurants, new RestaurantTransformer);
    }

    public function productsIndex(Restaurant $restaurant)
    {
        return $this->collectionResponse(
            $restaurant->products,
            new ProductTransformer
        );
    }

    public function productFormatsIndex(Product $product)
    {
        return $this->collectionResponse(
            $product->formats,
            new ProductFormatTransformer
        );
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->load('openingWindows', 'closingWindows');

        return $this->itemResponse($restaurant);
    }

    public function showAddress(Restaurant $restaurant)
    {
        return $this->itemResponse($restaurant->address);
    }
}
