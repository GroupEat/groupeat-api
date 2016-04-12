<?php

use Carbon\Carbon;

class AdminCest
{
    public function testThatOnlyAdminsCanSeeTheDocs(ApiTester $I)
    {
        $docsUrl = 'docs';
        list($customerToken) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($customerToken, $docsUrl);
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');

        $adminToken = $this->getAdminToken($I);

        $I->sendApiGetWithToken($adminToken, $docsUrl);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains("GroupEat RESTful API");
    }

    public function testThatGroupOrdersCanOnlyBeMadeUpByAdmins(ApiTester $I)
    {
        list(, $madeUpGroupOrderUrl, $madeUpGroupOrderRequestBody) = $this->getMadeUpGroupOrderDetails($I);

        list($customerToken) = $I->amAnActivatedCustomer();

        $I->sendApiPostWithToken($customerToken, $madeUpGroupOrderUrl, $madeUpGroupOrderRequestBody);
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');

        $adminToken = $this->getAdminToken($I);
        $I->sendApiPostWithToken($adminToken, $madeUpGroupOrderUrl, $madeUpGroupOrderRequestBody);
        $I->seeResponseCodeIs(201);
    }

    public function testThatTheInitialDiscountRateOfAMadeUpGroupOrderCannotExceedTheRestaurantMaximumDiscountRate(ApiTester $I)
    {
        list($customerToken) = $I->amAnActivatedCustomer();
        list($restaurantId, $madeUpGroupOrderUrl, $madeUpGroupOrderRequestBody) = $this->getMadeUpGroupOrderDetails($I);
        $I->sendApiGetWithToken($customerToken, "restaurants/$restaurantId");
        $maximumDiscountRate = $I->grabDataFromResponse('maximumDiscountRate');
        $madeUpGroupOrderRequestBody['initialDiscountRate'] = $maximumDiscountRate + 1;

        $adminToken = $this->getAdminToken($I);
        $I->sendApiPostWithToken($adminToken, $madeUpGroupOrderUrl, $madeUpGroupOrderRequestBody);
        $I->seeErrorResponse(400, 'initialDiscountRateTooBig');
    }

    public function testThatMadeUpGroupOrderBehaveAsExpected(ApiTester $I)
    {
        list($customerToken) = $I->amAnActivatedCustomer();
        list($restaurantId, $madeUpGroupOrderUrl, $madeUpGroupOrderRequestBody) = $this->getMadeUpGroupOrderDetails($I);

        $I->sendApiGetWithToken($customerToken, "restaurants/$restaurantId");
        $discountPolicy = $I->grabDataFromResponse('discountPolicy');
        $discountRates = array_values($discountPolicy);
        $discountPrices = array_keys($discountPolicy);
        $rawPriceIncreasingDiscountRate = $discountPrices[count($discountPrices) / 2];
        $initialDiscountRate = $discountRates[count($discountRates) / 2];

        $adminToken = $this->getAdminToken($I);
        $madeUpGroupOrderRequestBody['initialDiscountRate'] = $initialDiscountRate;
        $I->sendApiPostWithToken($adminToken, $madeUpGroupOrderUrl, $madeUpGroupOrderRequestBody);
        $I->seeResponseCodeIs(201);
        $I->assertEquals($initialDiscountRate, $I->grabDataFromResponse('discountRate'));
        $totalRawPrice = $I->grabDataFromResponse('totalRawPrice');
        $I->assertEquals(0, $totalRawPrice);
        $groupOrderId = $I->grabDataFromResponse('id');

        while ($totalRawPrice < $rawPriceIncreasingDiscountRate) {
            $I->sendApiGetWithToken($customerToken, "groupOrders/$groupOrderId");
            $I->assertEquals($initialDiscountRate, $I->grabDataFromResponse('discountRate'));
            $I->placeOrder();
            $I->sendApiGetWithToken($customerToken, "groupOrders/$groupOrderId");
            $totalRawPrice = $I->grabDataFromResponse('totalRawPrice');
        }

        $I->assertGreaterThanOrEqual($initialDiscountRate, $I->grabDataFromResponse('discountRate'));
    }

    private function getMadeUpGroupOrderDetails(ApiTester $I)
    {
        $restaurantId = $I->getIdOfRestaurantThatCanHandleAGroupOrder();

        return [
            $restaurantId,
            "restaurants/$restaurantId/madeUpGroupOrders",
            [
                'endingAt' => Carbon::now()->addMinutes(20)->toDateTimeString(),
                'initialDiscountRate' => 33
            ]
        ];
    }

    private function getAdminToken(ApiTester $I)
    {
        $I->sendApiPost('auth/token', ['email' => 'admin@groupeat.fr', 'password' => 'groupeat']);
        return $I->grabDataFromResponse('token');
    }
}
