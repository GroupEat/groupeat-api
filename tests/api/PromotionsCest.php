<?php

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\Promotion;
use Illuminate\Support\Collection;

class PromotionsCest
{
    public function testThatReachingTheThresholdGrantThePromotion(ApiTester $I)
    {
        $restaurantId = $I->getIdOfRestaurantThatCanHandleAGroupOrder();
        $promotionName = $this->addPromotion($restaurantId, 0, 1);
        list($token, $orderId) = $I->placeOrder();
        $I->assertContains($promotionName, $this->getOrderPromotionNames($I, $token, $orderId));
    }

    public function testThatThePromotionIsOnlyGrantedWhenTheThresholdIsReached(ApiTester $I)
    {
        $restaurantId = $I->getIdOfRestaurantThatCanHandleAGroupOrder();
        $unreachablePrice = 100000;
        $promotionName = $this->addPromotion($restaurantId, $unreachablePrice, 1);
        list($token, $orderId) = $I->placeOrder();
        $I->assertNotContains($promotionName, $this->getOrderPromotionNames($I, $token, $orderId));
    }

    public function testThatPromotionsAreGrantedRandomlyAccordingToTheBeneficiaryCount(ApiTester $I)
    {
        $beneficiaryCount = 2;
        $restaurantId = $I->getIdOfRestaurantThatCanHandleAGroupOrder();
        $promotionName = $this->addPromotion($restaurantId, 100, $beneficiaryCount);

        $tokens = [];
        $orderIds = [];
        $promotionCount = 0;
        for ($order = 0; $order < 5; $order++) {
            list($token, $orderId) = $I->placeOrder();
            $tokens[] = $token;
            $orderIds[] = $orderId;
        }
        for ($order = 0; $order < 5; $order++) {
            $promotionNames = $this->getOrderPromotionNames($I, $tokens[$order], $orderIds[$order]);
            if (array_search($promotionName, $promotionNames) !== false) {
                $promotionCount++;
            }
        }

        $I->assertEquals($beneficiaryCount, $promotionCount);
    }

    private function getOrderPromotionNames(ApiTester $I, $token, $orderId)
    {
        return $I->amInTheFuture(Carbon::now()->addHour(), function () use ($I, $token, $orderId) {
            $I->sendApiGetWithToken($token, "orders/$orderId?include=restaurantPromotions");
            $promotions = $I->grabDataFromResponse('restaurantPromotions.data');
            return empty($promotions) ? [] : Collection::make($promotions)->pluck('name')->all();
        });
    }

    private function addPromotion($restaurantId, $rawPriceThreshold, $beneficiaryCount, $name = 'PROMO')
    {
        $promotion = new Promotion;
        $promotion->restaurantId = $restaurantId;
        $promotion->rawPriceThreshold = $rawPriceThreshold;
        $promotion->beneficiaryCount = $beneficiaryCount;
        $promotion->name = $name;
        $promotion->save();

        return $name;
    }
}
