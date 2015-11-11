<?php

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Restaurants\Entities\ClosingWindow;
use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Values\PhoneNumber;

class RestaurantsCest
{
    public function testThatAUserShouldAuthenticateToSeeTheRestaurantList(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGet('restaurants');
        $I->seeResponseCodeIs(401);

        $I->sendApiGetWithToken($token, 'restaurants');
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

    public function testThatClosingAtOfAClosedRestaurantIsNow(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $I->sendApiGetWithToken($token, 'restaurants?opened=1');
        $response = $I->grabDataFromResponse();
        $restaurantIds = collect($response)->lists('id')->all();
        $I->assertNotContains($restaurantId, $restaurantIds);

        $I->sendApiGetWithToken($token, 'restaurants');
        $response = $I->grabDataFromResponse();
        $restaurantIds = collect($response)->lists('id')->all();
        $I->assertContains($restaurantId, $restaurantIds);
        $this->assertClosingAt($I, $token, $restaurantId, Carbon::now());
    }

    public function testThatClosingAtOfAnOpenedRestaurantIsTheEndOfItsCurrentOpeningWindow(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 21:00:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->assertClosingAt($I, $token, $restaurantId, Carbon::now());

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->assertClosingAt($I, $token, $restaurantId, '2015-11-07 23:59:59');

        $this->createOpeningWindow($restaurantId, '00:00:00', '01:00:00', true);
        $this->assertClosingAt($I, $token, $restaurantId, '2015-11-08 01:00:00');
    }

    public function testThatClosingAtOfAnRestaurantTakeClosingwindowsIntoAccount(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 21:00:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->createOpeningWindow($restaurantId, '00:00:00', '01:00:00', true);
        $this->createClosingWindow($restaurantId, '2015-11-08 00:30:00', '10:00:00');
        $this->assertClosingAt($I, $token, $restaurantId, '2015-11-08 00:30:00');
    }

    private function assertClosingAt($I, $token, $restaurantId, $expected)
    {
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId");
        $closingAt = $I->grabDataFromResponse('closingAt');
        $I->assertEquals($closingAt, (string) $expected);
    }

    private function createOpeningWindow($restaurantId, $start, $end, $tomorrow = false)
    {
        $openingWindow = new OpeningWindow;
        $openingWindow->restaurantId = $restaurantId;
        $openingWindow->dayOfWeek = $tomorrow ? Carbon::now()->addDay()->dayOfWeek : Carbon::now()->dayOfWeek;
        $openingWindow->start = $start;
        $openingWindow->end = $end;
        $openingWindow->save();
    }

    private function createClosingWindow($restaurantId, $start, $end)
    {
        $closingWindow = new ClosingWindow;
        $closingWindow->restaurantId = $restaurantId;
        $closingWindow->start = new Carbon($start);
        $closingWindow->end = new Carbon($end);
        $closingWindow->save();
    }

    private function createRestaurant()
    {
        $restaurant = new Restaurant;
        $restaurant->name = 'Test Pizza';
        $restaurant->phoneNumber = new PhoneNumber('33601020304');
        $restaurant->discountPolicy = [];
        $restaurant->minimumGroupOrderPrice = 0;
        $restaurant->deliveryCapacity = 0;
        $restaurant->rating = 0;
        $restaurant->pictureUrl = 'picture';
        $restaurant->save();

        $userCredentials = new UserCredentials;
        $userCredentials->user()->associate($restaurant);
        $userCredentials->email = uniqid() . '@pizza.fr';
        $userCredentials->password = 'groupeat';
        $userCredentials->locale = 'fr';
        $userCredentials->save();

        return $restaurant->id;
    }
}
