<?php
namespace Groupeat\Orders\Listeners;

use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenClosed;
use Groupeat\Restaurants\Entities\Promotion;
use Illuminate\Support\Collection;

class GrantPromotions
{
    public function handle(GroupOrderHasBeenClosed $event)
    {
        $groupOrder = $event->getGroupOrder();
        $totalRawPrice = $groupOrder->totalRawPrice;
        $restaurant = $groupOrder->restaurant;
        $restaurantPromotions = $restaurant->promotions;
        $relevantRestaurantPromotions = $restaurantPromotions->filter(function (Promotion $promotion) use ($totalRawPrice) {
            return $totalRawPrice->greaterThanOrEqual($promotion->rawPriceThreshold);
        });

        if ($relevantRestaurantPromotions->isEmpty()) {
            return;
        }

        $orders = $groupOrder->orders;

        $relevantRestaurantPromotions->each(function (Promotion $promotion) use ($orders) {
            $this->applyPromotion($promotion, $orders);
        });
    }

    protected function applyPromotion(Promotion $promotion, Collection $orders)
    {
        $beneficiaries = $this->selectBeneficiaries($promotion, $orders);

        $beneficiaries->each(function (Order $order) use ($promotion) {
            $order->restaurantPromotions()->attach($promotion);
        });
    }

    protected function selectBeneficiaries(Promotion $promotion, Collection $orders)
    {
        $beneficiaries = $promotion->beneficiaryCount ?
            $orders->shuffle()->take($promotion->beneficiaryCount) :
            $orders;

        return $beneficiaries->filter(function (Order $order) {
            return !$order->isExternal();
        });
    }
}
