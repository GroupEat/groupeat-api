<?php

use Carbon\Carbon;

class GroupOrdersCest
{
    public function testThatACustomerReceiveAMailWhenAGroupOrderIsConfirmed(ApiTester $I)
    {
        list($token, $orderId, $restaurantCapacity, $orderDetails) = $I->createGroupOrder();

        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $groupOrderId = $I->grabDataFromResponse('groupOrder.data.id');
        $I->assertTrue($I->grabDataFromResponse('groupOrder.data.joinable'));
        $remainingCapacity = $restaurantCapacity - 1;
        $I->assertSame($remainingCapacity, $I->grabDataFromResponse('groupOrder.data.remainingCapacity'));

        for ($i = $remainingCapacity; $i > 1; $i--) {
            $orderDetails['groupOrderId'] = $groupOrderId;
            $I->sendApiPostWithToken($token, 'orders', $orderDetails);
            $I->seeResponseCodeIs(201);
            $I->sendApiGetWithToken($token, "groupOrders/$groupOrderId");
            $I->assertSame($i - 1, $I->grabDataFromResponse('remainingCapacity'));
            $I->assertTrue($I->grabDataFromResponse('joinable'));
        }

        $orderDetails['groupOrderId'] = $groupOrderId;
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);
        $mail = $I->grabMailById('restaurants.groupOrderHasBeenClosed');

        list(, $restaurantToken) = explode(
            'token=',
            $I->grabHrefInLinkByIdInMail($mail, 'confirm-group-order-link')
        );

        $confirmUrl = "groupOrders/$groupOrderId/confirm";

        $I->sendApiGetWithToken($restaurantToken, "groupOrders/$groupOrderId");
        $I->assertSame(0, $I->grabDataFromResponse('remainingCapacity'));
        $I->assertFalse($I->grabDataFromResponse('joinable'));

        $I->sendApiPostWithToken($restaurantToken, $confirmUrl, [
            'preparedAt' => (string) \Carbon\Carbon::now()->subMinute(),
        ]);
        $I->seeErrorResponse(422, 'cannotBePreparedBeforeBeingClosed');

        $I->sendApiPostWithToken($restaurantToken, $confirmUrl, [
            'preparedAt' => (string) \Carbon\Carbon::now()->addHours(2),
        ]);
        $I->seeErrorResponse(422, 'preparationTimeTooLong');

        $I->sendApiPostWithToken($restaurantToken, $confirmUrl, [
            'preparedAt' => (string) \Carbon\Carbon::now()->addMinutes(10),
        ]);
        $I->seeResponseCodeIs(200);

        $I->assertSame('customers.orderHasBeenConfirmed', $I->grabFirstMailId());

        $I->sendApiPostWithToken($restaurantToken, $confirmUrl, [
            'preparedAt' => (string) \Carbon\Carbon::now()->addMinutes(10),
        ]);
        $I->seeErrorResponse(422, 'alreadyConfirmed');
    }

    public function testThatAGroupOrderClosesAutomaticallyWhenFoodrushIsOver(ApiTester $I)
    {
        list($token, $orderId) = $I->createGroupOrder();

        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $I->assertTrue($I->grabDataFromResponse('groupOrder.data.joinable'));

        $I->runArtisan('group-orders:close');
        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $I->assertTrue($I->grabDataFromResponse('groupOrder.data.joinable'));

        $I->runArtisan('group-orders:close', ['--minutes' => 31]);
        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $I->assertFalse($I->grabDataFromResponse('groupOrder.data.joinable'));
    }

    public function createGroupOrder(ApiTester $I)
    {
        list($token, $customerId) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken(
            $token,
            'groupOrders?joinable=1&around=1&latitude=48.7173&longitude=2.23935&include=restaurant'
        );
        $groupOrders = $I->grabDataFromResponse('');

        if (!empty($groupOrders)) {
            $groupOrderId = $groupOrders[0]['id'];
            $restaurantId = $groupOrders[0]['restaurant']['data']['id'];
        } else {
            $groupOrderId = null;
            $I->sendApiGetWithToken($token, 'restaurants?opened=1&around=1&latitude=48.7173&longitude=2.23935');
            $restaurants = $I->grabDataFromResponse();
            $restaurantId = $restaurants[0]['id'];
        }

        $I->sendApiGetWithToken($token, "restaurants/$restaurantId");
        $restaurantCapacity = $I->grabDataFromResponse('deliveryCapacity');
        $I->assertGreaterThan(1, $restaurantCapacity);
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/products?include=formats");
        $productFormatId = last(last($I->grabDataFromResponse())['formats']['data'])['id'];
        $productFormats = [$productFormatId => 1];

        $orderDetails = [
            'foodRushDurationInMinutes' => 30,
            'productFormats' => $productFormats,
            'street' => "Allée des techniques avancées",
            'details' => "Bâtiment A, chambre 200",
            'latitude' => 48.7173,
            'longitude' => 2.23935,
        ];

        if (!is_null($groupOrderId)) {
            $orderDetails['groupOrderId'] = $groupOrderId;
        }

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $I->grabDataFromResponse('id');

        return [$token, $orderId, $restaurantCapacity, $orderDetails, $customerId];
    }
}
