<?php

class OrdersCest {

    public function testThatAUserShouldBeActivatedToPlaceAnOrder(ApiTester $I)
    {
        list($token) = $I->sendRegistrationRequest();
        $orderDetails = $this->getOrderDetails($I, $token);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(403, 'userShouldBeActivated');

        list($token) = $I->amAnActivatedCustomer();
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);
    }

    public function testThatTheRestaurantReceiveAnEmailWhenAnOrderIsPlaced(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $productFormatId = array_keys($orderDetails['productFormats'])[0];
        $restaurantEmail = $this->getRestaurantEmailFromProductFormat($productFormatId);
        $I->assertEquals('restaurants.orderHasBeenPlaced', $I->grabLastMailId());
        $I->assertEquals($restaurantEmail, $I->grabLastMailRecipient());
    }

    public function testThatTheFoodRushDurationMustBeValid(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token, ['foodRushDurationInMinutes' => 70]);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'invalidFoodRushDuration');

        $orderDetails['foodRushDurationInMinutes'] = 1;
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'invalidFoodRushDuration');
    }

    public function testThatTheOrderCannotBeEmpty(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token, ['productFormats' => []]);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'noProductFormats');
    }

    public function testThatTheProductFormatsMustExist(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token, ['productFormats' => [66666 => 1]]);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(404, 'unexistingProductFormats');
    }

    public function testThatTheAnOrderCannotBePlacedOnAClosedRestaurant(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $products = $this->getProducts($I, $token, ['around' => true, 'opened' => false], function($restaurants)
        {
            foreach ($restaurants as $restaurant)
            {
                if (!$restaurant['opened'])
                {
                    return $restaurant['id'];
                }
            }
        });
        $productFormats = $this->getProductFormatsFrom($products);
        $orderDetails = $this->getOrderDetails($I, $token, compact('productFormats'));

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'restaurantClosed');
    }

    public function testThatTheRestaurantMustBeCloseEnough(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $latitude = 0;
        $longitude = 0;
        $products = $this->getProducts(
            $I,
            $token,
            ['around' => true, 'opened' => true],
            function($restaurants) use ($latitude, $longitude)
        {
            $restaurant = $restaurants[0];
            $latitude = $restaurant['address']['data']['latitude'];
            $longitude = $restaurant['address']['data']['longitude'];

            return $restaurant['id'];
        });

        $productFormats = $this->getProductFormatsFrom($products);
        $orderDetails = $this->getOrderDetails($I, $token, [
            'productFormats' => $productFormats,
            'latitude' => $latitude - 1,
            'longitude' => $longitude + 1,
        ]);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'deliveryDistanceTooLong');
    }

    public function testThatTheProductFormatsMustBelongToTheSameRestaurant(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $I->sendApiGetWithToken($token, 'restaurants');

        foreach ($I->grabDataFromResponse('') as $restaurant)
        {
            if (!$restaurant['opened'])
            {
                $wrongRestaurantId = $restaurant['id'];
                break;
            }
        }

        $wrongProducts = $this->getProducts(
            $I,
            $token,
            ['around' => true, 'opened' => true],
            function() use ($wrongRestaurantId)
        {
            return $wrongRestaurantId;
        });

        $wrongProductFormats = $this->getProductFormatsFrom($wrongProducts);

        $orderDetails = $this->getOrderDetails($I, $token);

        $orderDetails['productFormats'] = $wrongProductFormats + $orderDetails['productFormats'];

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'productFormatsFromDifferentRestaurants');
    }

    public function testThatTheOrderMustExceedTheMinimumPrice(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $options = ['around' => true, 'opened' => true];
        $minimumPrice = 0;

        $products = $this->getProducts($I, $token, $options, function($restaurants) use (&$minimumPrice)
        {
            $minimumPrice = $restaurants[0]['minimumOrderPrice'];

            return $restaurants[0]['id'];
        });

        $price = 0;
        $productFormats = [];

        foreach ($products as $product)
        {
            foreach ($product['formats']['data'] as $format)
            {
                if (($price + $format['price']) < $minimumPrice)
                {
                    $price += $format['price'];
                    $productFormats[$format['id']] = 1;
                }
            }
        }

        $orderDetails = $this->getOrderDetails($I, $token, compact('productFormats'));
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'minimumOrderPriceNotReached');
    }

    public function testThatTheOrderShouldNotExceedRestaurantDeliveryCapacity(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $options = ['around' => true, 'opened' => true];
        $deliveryCapacity = 0;

        $products = $this->getProducts($I, $token, $options, function($restaurants) use (&$deliveryCapacity)
        {
            $deliveryCapacity = $restaurants[0]['deliveryCapacity'];

            return $restaurants[0]['id'];
        });

        $amount = 0;
        $productFormats = [];

        foreach ($products as $product)
        {
            foreach ($product['formats']['data'] as $format)
            {
                if ($amount <= $deliveryCapacity)
                {
                    $amount++;
                    $productFormats[$format['id']] = 1;
                }
            }
        }

        $orderDetails = $this->getOrderDetails($I, $token, compact('productFormats'));
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'restaurantDeliveryCapacityExceeded');
    }

    public function testThatARestaurantCanHaveOnlyOneGroupOrderAtTheSameTime(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'groupOrderAlreadyExisting');
    }

    public function testThatGroupOrdersCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $I->sendApiGetWithToken($token, 'groupOrders');
        $I->seeResponseCodeIs(200);
    }

    public function testThatAnOrderCanBeShownByTheCustomerWhoPlacedItOnly(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $I->grabDataFromResponse('id');
        $I->sendApiGetWithToken($token, "orders/$orderId");
        $I->seeResponseCodeIs(200);
        $I->assertEquals($orderId, $I->grabDataFromResponse('id'));

        list($token2) = $I->sendRegistrationRequest();
        $I->sendApiGetWithToken($token2, "orders/$orderId");
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');
    }

    public function testThatTheDiscountRateIncreaseWhenAGroupOrderIsJoined(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $I->grabDataFromResponse('id');
        $oldRawPrice = $I->grabDataFromResponse('rawPrice');

        $oldDiscountRate = $this->computeDiscountRate(
            $I->grabDataFromResponse('discountedPrice'),
            $oldRawPrice
        );
        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $groupOrderId = $I->grabDataFromResponse('groupOrder.data.id');

        $orderDetails['groupOrderId'] = $groupOrderId;
        unset($orderDetails['foodRushDurationInMinutes']);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);
        $newDiscountRate = $this->computeDiscountRate(
                $I->grabDataFromResponse('discountedPrice'),
                $I->grabDataFromResponse('rawPrice')
        );
        $I->sendApiGetWithToken($token , "groupOrders/$groupOrderId");
        $newDiscountRateFormGroupOrder = $I->grabDataFromResponse('discountRate');

        $I->assertGreaterThan($oldDiscountRate, $newDiscountRate);
        $I->assertEquals($newDiscountRate, $newDiscountRateFormGroupOrder);

        $I->sendApiGetWithToken($token, "orders/$orderId");
        $newRawPrice = $I->grabDataFromResponse('rawPrice');
        $I->assertEquals($oldRawPrice, $newRawPrice);
        $newDiscountRateFromOrder = $this->computeDiscountRate(
            $I->grabDataFromResponse('discountedPrice'),
            $newRawPrice
        );
        $I->assertEquals($newDiscountRate, $newDiscountRateFromOrder);
    }

    public function testThatTheDeliveryAddressMustBeCloseEnoughToJoinAGroupOrder(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $I->grabDataFromResponse('id');
        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $groupOrderId = $I->grabDataFromResponse('groupOrder.data.id');

        $orderDetails['groupOrderId'] = $groupOrderId;
        $orderDetails['latitude']++;
        unset($orderDetails['foodRushDurationInMinutes']);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'deliveryDistanceTooLong');
    }

    private function getProducts(
        ApiTester $I,
        $token,
        array $options = ['around' => true, 'opened' => true],
        Closure $getRestaurantIdCallback = null
    )
    {
        if (is_null($getRestaurantIdCallback))
        {
            $getRestaurantIdCallback = function($restaurants)
            {
                return $restaurants[0]['id'];
            };
        }

        $restaurantsUrl = 'restaurants?'.http_build_query($this->getQueryStringParamsFor($options));
        $I->sendApiGetWithToken($token, $restaurantsUrl);
        $restaurantId = $getRestaurantIdCallback($I->grabDataFromResponse(''));
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/products?include=formats");

        return $I->grabDataFromResponse('');
    }

    private function getOrderDetails(ApiTester $I, $token, array $details = [])
    {
        $defaultProductFormats = '{}';

        if (empty($details['productFormats']))
        {
            $defaultProductFormats = $this->getProductFormatsFrom($this->getProducts($I, $token));
        }

        $details = array_merge([
            'foodRushDurationInMinutes' => 30,
            'productFormats' => $defaultProductFormats,
            'street' => "Allée des techniques avancées",
            'details' => "Bâtiment A, chambre 200",
            'latitude' => $this->getDefaultLatitude(),
            'longitude' => $this->getDefaultLongitude(),
        ], $details);

        return $details;
    }

    private function getQueryStringParamsFor(array $options = [])
    {
        $latitude = isset($options['latitude']) ? $options['latitude'] : $this->getDefaultLatitude();
        $longitude = isset($options['longitude']) ? $options['longitude'] : $this->getDefaultLongitude();

        $queryStringParams = [];

        if (!empty($options['around']))
        {
            $queryStringParams['include'][] = 'address';
            $queryStringParams['around'] = 'true';
            $queryStringParams['latitude'] = $latitude;
            $queryStringParams['longitude'] = $longitude;
        }

        if (!empty($options['opened']))
        {
            $queryStringParams['opened'] = 'true';
        }

        $queryStringParams['include'] = implode(',', $queryStringParams['include']);

        return $queryStringParams;
    }

    private function getProductFormatsFrom(array $products, array $selectedProducts = [[0,0,2], [1,2,1]])
    {
        $productFormats = [];

        foreach ($selectedProducts as $selectedProduct)
        {
            $productFormats[$products[$selectedProduct[0]]['formats']['data'][$selectedProduct[1]]['id']]
                = $selectedProduct[2];
        }

        return $productFormats;
    }

    private function getDefaultLatitude()
    {
        return 48.7173;
    }

    private function getDefaultLongitude()
    {
        return 2.23935;
    }

    private function getRestaurantEmailFromProductFormat($productFormatId)
    {
        return \Groupeat\Restaurants\Entities\ProductFormat::find($productFormatId)->product->restaurant->email;
    }

    private function computeDiscountRate($discountedPrice, $rawPrice)
    {
        return (int) round(100 * (1 - $discountedPrice / $rawPrice));
    }

}
