<?php

class OrdersCest {

    public function testThatAUserShouldBeActivatedToPassAnOrder(ApiTester $I)
    {
        list($token) = $I->sendRegistrationRequest();
        $products = $this->getProducts($I, $token, ['around' => true, 'opened' => true]);
        $orderDetails = $this->getOrderDetails($I, $token);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(403, 'userShouldBeActivated');

        list($token) = $I->amAnActivatedCustomer();
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);
    }

    public function testThatTheFoodRushDurationCannotBeTooLong(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token, ['foodRushDurationInMinutes' => 70]);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'foodRushTooLong');
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
            ['around' => true],
            function($restaurants) use ($latitude, $longitude)
        {
            $restaurant = $restaurants[0];
            $latitude = $restaurant['latitude'];
            $longitude = $restaurant['longitude'];

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

        $productFormatsFromDifferentRestaurants = $wrongProductFormats
            + json_decode($orderDetails['productFormats'], true);

        $orderDetails['productFormats'] = json_encode($productFormatsFromDifferentRestaurants);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'productFormatsFromDifferentRestaurants');
    }

    private function getProducts(
        ApiTester $I,
        $token,
        array $options = ['around' => true, 'opened' => true],
        Closure $getRestaurantIdCallback = null
    )
    {
        $latitude = isset($options['latitude']) ? $options['latitude'] : $this->getDefaultLatitude();
        $longitude = isset($options['longitude']) ? $options['longitude'] : $this->getDefaultLongitude();

        $queryStringParams = [];

        if (!empty($options['around']))
        {
            $queryStringParams['around'] = 'true';
            $queryStringParams['latitude'] = $latitude;
            $queryStringParams['longitude'] = $longitude;
        }

        if (!empty($options['opened']))
        {
            $queryStringParams['opened'] = 'true';
        }

        if (is_null($getRestaurantIdCallback))
        {
            $getRestaurantIdCallback = function($restaurants)
            {
                return $restaurants[0]['id'];
            };
        }

        $restaurantsUrl = 'restaurants?'.http_build_query($queryStringParams);
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

        $details['productFormats'] = json_encode($details['productFormats']);

        return $details;
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
        return 48.711042;
    }

    private function getDefaultLongitude()
    {
        return 2.219278;
    }

}
