<?php

class ExternalOrdersCest
{
    public function testThatARestaurantCanPushAnExternalOrder(ApiTester $I)
    {
        list($token, $id) = $I->amAlloPizzaRestaurant();

        $I->sendApiGetWithToken($token, "restaurants/$id/groupOrders");
        $data = $I->grabDataFromResponse();
        $I->assertEmpty($data);

        $I->sendApiGetWithToken($token, "restaurants/$id/products?include=formats");
        $products = $I->grabDataFromResponse();
        $productFormatId = $products[0]['formats']['data'][0]['id'];

        $orderData = [
            'productFormats' => [$productFormatId => 3],
            'deliveryAddress' => [
                'street' => "ue de la pizza",
                'details' => "dernier étage",
                'city' => "PizzaCity",
                'postcode' => "75001",
                'state' => "PizzaState",
                'country' => "PizzaLand",
                'latitude' => 48.711042,
                'longitude' => 2.219278
            ],
            'customer' => [
                'phoneNumber' => '33605040306'
            ],
            'comment' => "J'aurais dû passer via l'app GroupEat...",
        ];

        $otherRestaurantId = $id - 1;
        $I->sendApiPostWithToken($token, "restaurants/$otherRestaurantId/externalOrders", $orderData);
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');

        $I->sendApiPostWithToken($token, "restaurants/$id/externalOrders", $orderData);
        $I->seeResponseCodeIs(201);
        $data = $I->grabDataFromResponse();

        $mails = $I->grabMails();
        $I->assertEquals(1, $mails->count());
        $I->assertEquals('restaurants.orderHasBeenPlaced', $I->grabMailId($mails->first()));
        $I->assertFirstMailContains('06 05 04 03 06'); // The phone number should be correctly formatted.

        $I->assertNoSmsWasSent();

        $I->assertEquals($data['rawPrice'], $data['discountedPrice']);
        $I->sendApiGetWithToken($token, 'orders/' . $data['id'] . '?include=groupOrder');
        $groupOrderData = $I->grabDataFromResponse()['groupOrder']['data'];
        $I->assertEquals($data['rawPrice'], $groupOrderData['totalRawPrice']);
        $I->assertGreaterThan(0, $groupOrderData['discountRate']);
    }
}
