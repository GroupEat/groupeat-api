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
            $I->sendApiPostWithToken($token, "groupOrders/$groupOrderId/orders", $orderDetails);
            $I->seeResponseCodeIs(201);
            $I->sendApiGetWithToken($token, "groupOrders/$groupOrderId");
            $I->assertSame($i - 1, $I->grabDataFromResponse('remainingCapacity'));
            $I->assertTrue($I->grabDataFromResponse('joinable'));
        }

        $orderDetails['groupOrderId'] = $groupOrderId;
        $I->sendApiPostWithToken($token, "groupOrders/$groupOrderId/orders", $orderDetails);
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
            'preparedAt' => (string) Carbon::now()->subMinute(),
        ]);
        $I->seeErrorResponse(422, 'cannotBePreparedBeforeBeingClosed');

        $I->sendApiPostWithToken($restaurantToken, $confirmUrl, [
            'preparedAt' => (string) \Carbon\Carbon::now()->addHours(2),
        ]);
        $I->seeErrorResponse(422, 'preparationTimeTooLong');

        $I->sendApiPostWithToken($restaurantToken, $confirmUrl, [
            'preparedAt' => (string) Carbon::now()->addMinutes(10),
        ]);
        $I->seeResponseCodeIs(200);

        $I->assertSame('customers.orderHasBeenConfirmed', $I->grabFirstMailId());

        $I->sendApiPostWithToken($restaurantToken, $confirmUrl, [
            'preparedAt' => (string) Carbon::now()->addMinutes(10),
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

    public function testThatARestaurantCanListItsGroupOrder(ApiTester $I)
    {
        list($token, $id) = $I->amAlloPizzaRestaurant();

        $I->sendApiGetWithToken($token, 'restaurants/'.($id - 1).'/groupOrders');
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');

        $I->sendApiGetWithToken($token, "restaurants/$id/groupOrders");
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsData([]);

        list($customerToken, $customerId) = $I->amAnActivatedCustomer();
        $I->sendApiGetWithToken($customerToken, 'restaurants?opened=1&around=1&latitude=48.855118&longitude=2.345730');
    }
}
