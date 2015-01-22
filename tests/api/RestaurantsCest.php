<?php

class RestaurantsCest {

    public function testThatAUserShouldAuthenticateToSeeTheRestaurantList(ApiTester $I)
    {
        list($token, $id) = $I->sendRegistrationRequest();

        $I->sendApiGet('restaurants');
        $I->seeResponseCodeIs(401);

        $I->sendApiGetWithToken($token, 'restaurants');
        $I->seeResponseCodeIs(200);
    }

    public function testThatTheCategoriesCanBeListed(ApiTester $I)
    {
        list($token, $id) = $I->sendRegistrationRequest();

        $I->sendApiGetWithToken($token, 'restaurant-categories');
        $I->seeResponseCodeIs(200);
    }

}
