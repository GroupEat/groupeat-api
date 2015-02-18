<?php

class RestaurantsCest {

    public function testThatAUserShouldAuthenticateToSeeTheRestaurantList(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGet('restaurants');
        $I->seeResponseCodeIs(401);

        $I->sendApiGetWithToken($token, 'restaurants');
        $I->seeResponseCodeIs(200);
    }

    public function testThatTheCategoriesCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, 'restaurantCategories');
        $I->seeResponseCodeIs(200);
    }

    public function testThatTheFoodTypesCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, 'foodTypes');
        $I->seeResponseCodeIs(200);
    }

    public function testThatTheProductsOfARestaurantCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, 'restaurants/1/products');
        $I->seeResponseCodeIs(200);
    }

    public function testThatTheFormatsOfAProductCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, 'restaurants/1/products');
        $I->seeResponseCodeIs(200);
        $product = $I->grabDataFromResponse()[0];

        $I->sendApiGetWithToken($token, 'products/'.$product['id'].'/formats');
        $I->seeResponseCodeIs(200);
    }

}
