<?php

class OrdersCest {

    private $defaultProductFormats;


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
        $orderDetails = $this->getOrderDetails($I, $token);

        $orderDetails['foodRushDurationInMinutes'] = 70;

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'foodRushTooLong');
    }

    public function testThatTheOrderCannotBeEmpty(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token);

        $orderDetails['productFormats'] = '{}';

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'emptyOrder');
    }

    private function getProducts(ApiTester $I, $token, array $options = [])
    {
        $latitude = isset($options['latitude']) ? $options['latitude'] : $this->getDefaultLatitude();
        $longitude = isset($options['longitude']) ? $options['longitude'] : $this->getDefaultLongitude();

        $queryStringParams = [];

        if (isset($options['around']))
        {
            $queryStringParams['around'] = 'true';
            $queryStringParams['latitude'] = $latitude;
            $queryStringParams['longitude'] = $longitude;
        }

        if (!empty($options['opened']))
        {
            $queryStringParams['opened'] = 'true';
        }

        $restaurantsUrl = 'restaurants?'.http_build_query($queryStringParams);
        $I->sendApiGetWithToken($token, "restaurants?opened=true&around=true&latitude=$latitude&longitude=$longitude");
        $I->seeResponseCodeIs(200);
        $restaurantId = $I->grabDataFromResponse('')[0]['id'];
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/products?include=formats");
        $I->seeResponseCodeIs(200);

        return $I->grabDataFromResponse('');
    }

    private function getOrderDetails(ApiTester $I, $token, array $details = [])
    {
        if (empty($this->defaultProductFormats))
        {
            $products = $this->getProducts($I, $token);

            $this->defaultProductFormats = json_encode([
                $products[0]['formats']['data'][0]['id'] => 2,
                $products[1]['formats']['data'][2]['id'] => 1,
            ]);
        }

        return array_merge($details, [
            'foodRushDurationInMinutes' => 30,
            'productFormats' => $this->defaultProductFormats,
            'street' => "Allée des techniques avancées",
            'details' => "Bâtiment A, chambre 200",
            'latitude' => $this->getDefaultLatitude(),
            'longitude' => $this->getDefaultLongitude(),
        ]);
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
