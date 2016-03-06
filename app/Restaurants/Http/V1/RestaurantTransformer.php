<?php
namespace Groupeat\Restaurants\Http\V1;

use Groupeat\Restaurants\Entities\Restaurant;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;
use League\Period\Period;

class RestaurantTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['address', 'categories', 'openedWindows'];

    public function transform(Restaurant $restaurant)
    {
        return [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'email' => $restaurant->email,
            'rating' => $restaurant->rating,
            'isOpened' => $restaurant->isOpened(),
            'closingAt' => (string) $restaurant->closingAt,
            'phoneNumber' => $restaurant->phoneNumber,
            'minimumGroupOrderPrice' => $restaurant->minimumGroupOrderPrice->getAmount(),
            'deliveryCapacity' => $restaurant->deliveryCapacity,
            'pictureUrl' => $restaurant->pictureUrl,
            'discountPolicy' => $restaurant->discountPolicy,
            'maximumDiscountRate' => $restaurant->maximumDiscountRate
        ];
    }

    public function includeAddress(Restaurant $restaurant)
    {
        return $this->item($restaurant->address, new AddressTransformer);
    }

    public function includeCategories(Restaurant $restaurant)
    {
        return $this->collection($restaurant->categories, new CategoryTransformer);
    }

    public function includeOpenedWindows(Restaurant $restaurant)
    {
        $now = Carbon::now();
        $openedWindows = $restaurant->getOpenedWindows(new Period($now, $now->copy()->addWeek(1)));
        return $this->collection($openedWindows, new PeriodTransformer);
    }
}
