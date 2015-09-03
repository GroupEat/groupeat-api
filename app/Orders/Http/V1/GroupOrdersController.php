<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Orders\Http\V1\Traits\CanAddOrder;
use Groupeat\Orders\Jobs\ConfirmGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Jobs\JoinGroupOrder;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Phaza\LaravelPostgis\Geometries\Point;

class GroupOrdersController extends Controller
{
    use CanAddOrder;

    public function index()
    {
        $query = GroupOrder::with('restaurant');

        if ((bool) $this->get('joinable')) {
            $query->joinable();
        }

        if ((bool) $this->get('around')) {
            $query->around(new Point($this->get('latitude'), $this->get('longitude')));
        }

        return $this->collectionResponse($query->get(), new GroupOrderTransformer);
    }

    public function indexForRestaurant(Restaurant $restaurant)
    {
        $this->auth->assertSame($restaurant);

        return $this->collectionResponse(GroupOrder::where('restaurantId', $restaurant->id)->get(), new GroupOrderTransformer);
    }

    public function show(GroupOrder $groupOrder)
    {
        return $this->itemResponse($groupOrder);
    }

    public function confirm(GroupOrder $groupOrder)
    {
        $this->auth->assertSame($groupOrder->restaurant);

        $this->dispatch(new ConfirmGroupOrder($groupOrder, $this->json('preparedAt')));
    }

    public function join(GroupOrder $groupOrder)
    {
        return $this->addOrder(function ($productFormats, $deliveryAddressData, $comment) use ($groupOrder) {
            return new JoinGroupOrder(
                $groupOrder,
                $this->auth->customer(),
                $productFormats,
                $deliveryAddressData,
                $comment
            );
        });
    }
}
