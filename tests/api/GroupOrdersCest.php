<?php

class GroupOrdersCest {

    public function testThatACustomerReceiveAMailWhenAGroupOrderIsConfirmed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, 'restaurants?opened=true&around=true&latitude=48.7173&longitude=2.23935');
        $restaurants = $I->grabDataFromResponse('');
        $restaurantId = $restaurants[0]['id'];
        $restaurantCapacity = $restaurants[0]['deliveryCapacity'];
        $I->assertGreaterThan(1, $restaurantCapacity);
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/products?include=formats");
        $productFormatId = last(last($I->grabDataFromResponse(''))['formats']['data'])['id'];
        $productFormats = json_encode([$productFormatId => 1]);

        $orderDetails = [
            'foodRushDurationInMinutes' => 30,
            'productFormats' => $productFormats,
            'street' => "Allée des techniques avancées",
            'details' => "Bâtiment A, chambre 200",
            'latitude' => 48.7173,
            'longitude' => 2.23935,
        ];

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $I->grabDataFromResponse('id');
        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $groupOrderId = $I->grabDataFromResponse('groupOrder.data.id');
        $I->assertTrue($I->grabDataFromResponse('groupOrder.data.joinable'));
        $remainingCapacity = $restaurantCapacity - 1;
        $I->assertEquals($remainingCapacity, $I->grabDataFromResponse('groupOrder.data.remainingCapacity'));

        for ($i = $remainingCapacity; $i > 1; $i--)
        {
            $orderDetails['groupOrderId'] = $groupOrderId;
            $I->sendApiPostWithToken($token, 'orders', $orderDetails);
            $I->seeResponseCodeIs(201);
            $I->sendApiGetWithToken($token, "groupOrders/$groupOrderId");
            $I->assertEquals($i - 1, $I->grabDataFromResponse('remainingCapacity'));
            $I->assertTrue($I->grabDataFromResponse('joinable'));
        }

        $orderDetails['groupOrderId'] = $groupOrderId;
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);
        $I->assertEquals('restaurants.groupOrderHasEnded', $I->grabLastMailId());
        $confirmUrl = $I->grabHrefInLinkByIdInLastMail('confirm-group-order-link');

        $I->sendApiGetWithToken($token, "groupOrders/$groupOrderId");
        $I->assertEquals(0, $I->grabDataFromResponse('remainingCapacity'));
        $I->assertFalse($I->grabDataFromResponse('joinable'));

        $I->sendGET($confirmUrl);
        $I->seeResponseCodeIs(200);
        $preparedAtField = $I->grabCrawlableResponse()->filter('#preparedAt')->html();
        $I->assertNotEmpty($preparedAtField);

        $I->sendPOST($confirmUrl, ['preparedAt' => \Carbon\Carbon::now()->subMinute()]);
        $I->seeResponseCodeIs(200);
        $I->dontSeeSuccessfulPanel();

        $I->sendPOST($confirmUrl, ['preparedAt' => \Carbon\Carbon::now()->addHours(2)]);
        $I->seeResponseCodeIs(200);
        $I->dontSeeSuccessfulPanel();

        $I->sendPOST($confirmUrl, ['preparedAt' => \Carbon\Carbon::now()->addMinutes(10)]);
        $I->seeSuccessfulPanel();

        $I->assertEquals('customers.orderHasBeenConfirmed', $I->grabLastMailId());
    }

}
