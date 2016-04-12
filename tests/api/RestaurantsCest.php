<?php

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Orders\Values\MaximumOrderFlowInMinutes;
use Groupeat\Restaurants\Entities\ClosingWindow;
use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Values\PhoneNumber;
use League\Period\Period;
use Illuminate\Database\Eloquent\Collection;

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

    public function testThatTheOpenedQueryParameterWorks(ApiTester $I)
    {
        $maximumOrderFlowInMinutes = $I->getValue(MaximumOrderFlowInMinutes::class);

        $now = new Carbon('2015-11-07 21:00:00');
        Carbon::setTestNow($now);
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $I->assertTrue($this->isRestaurantInResponse($I, $token, $restaurantId, ''));
        $I->assertTrue($this->isRestaurantInResponse($I, $token, $restaurantId, 'opened=0'));
        $I->assertFalse($this->isRestaurantInResponse($I, $token, $restaurantId, 'opened=1'));

        $this->createOpeningWindow($restaurantId, $now->toTimeString(), $now->toTimeString());
        $I->assertFalse($this->isRestaurantInResponse($I, $token, $restaurantId, 'opened=1'));

        $this->createOpeningWindow($restaurantId, $now->toTimeString(), $now->toTimeString());
        $I->assertFalse($this->isRestaurantInResponse($I, $token, $restaurantId, 'opened=1'));

        $this->createOpeningWindow($restaurantId, $now->toTimeString(), $now->copy()->addMinutes(
            $maximumOrderFlowInMinutes
        )->toTimeString());
        $I->assertTrue($this->isRestaurantInResponse($I, $token, $restaurantId, 'opened=1'));
    }

    public function testThatClosingAtOfAClosedRestaurantIsNow(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $I->assertFalse($this->isRestaurantInResponse($I, $token, $restaurantId, 'opened=1'));

        $I->assertTrue($this->isRestaurantInResponse($I, $token, $restaurantId, ''));
        $this->assertClosingAt($I, $token, $restaurantId, Carbon::now());
    }

    public function testThatClosingAtOfAnOpenedRestaurantIsTheEndOfItsCurrentOpeningWindow(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 21:00:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->assertClosingAt($I, $token, $restaurantId, Carbon::now());

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->assertClosingAt($I, $token, $restaurantId, new Carbon('2015-11-07 23:59:59'));

        $this->createOpeningWindow($restaurantId, '00:00:00', '01:00:00', true);
        $this->assertClosingAt($I, $token, $restaurantId, new Carbon('2015-11-08 01:00:00'));
    }

    public function testThatClosingAtOfAnRestaurantTakeClosingwindowsIntoAccount(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 21:00:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->createOpeningWindow($restaurantId, '00:00:00', '01:00:00', true);
        $this->createClosingWindow($restaurantId, '2015-11-08 00:30:00', '10:00:00');
        $this->assertClosingAt($I, $token, $restaurantId, new Carbon('2015-11-08 00:30:00'));
    }

    public function testThatOpeningWindowsShouldSpanForAWeek(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 20:30:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59', true);
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/?include=openingWindows");
        $openingWindows = $I->grabDataFromResponse('openingWindows.data');
        // Two for today's opening window and one for tomorrow's
        $I->assertEquals(count($openingWindows), 3);
        $this->assertPeriodEquals($I, $openingWindows[0], $this->makePeriod('2015-11-07 20:30:00', '2015-11-07 23:59:59'));
    }

    public function testThatOpeningWindowsShouldBeSplatByClosingWindows(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 20:30:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->createClosingWindow($restaurantId, '2015-11-07 21:30:00', '2015-11-07 21:45:00');
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/?include=openingWindows");
        $openingWindows = Collection::make($I->grabDataFromResponse('openingWindows.data'));
        // Today's opened window should be splat in two
        $I->assertEquals(count($openingWindows), 3);
        $this->assertPeriodEquals($I, $openingWindows[0], $this->makePeriod('2015-11-07 20:30:00', '2015-11-07 21:30:00'));
        $this->assertPeriodEquals($I, $openingWindows[1], $this->makePeriod('2015-11-07 21:45:00', '2015-11-07 23:59:59'));
    }

    public function testThatOpeningWindowsShouldNotStartBeforeNow(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 21:00:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->createClosingWindow($restaurantId, '2015-11-07 21:30:00', '2015-11-07 21:45:00');
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/?include=openingWindows");
        $openingWindows = Collection::make($I->grabDataFromResponse('openingWindows.data'));
        // Today's opened window should be splat in two
        $I->assertEquals(count($openingWindows), 3);
        $this->assertPeriodEquals($I, $openingWindows[0], $this->makePeriod('2015-11-07 21:00:00', '2015-11-07 21:30:00'));
        $this->assertPeriodEquals($I, $openingWindows[1], $this->makePeriod('2015-11-07 21:45:00', '2015-11-07 23:59:59'));
    }

    public function testThatOpeningWindowsSupportRestaurantOpenedAfterMidnight(ApiTester $I)
    {
        Carbon::setTestNow(new Carbon('2015-11-07 20:30:00'));
        list($token) = $I->amAnActivatedCustomer();
        $restaurantId = $this->createRestaurant();

        $this->createOpeningWindow($restaurantId, '20:30:00', '23:59:59');
        $this->createOpeningWindow($restaurantId, '00:00:00', '00:30:00', true);
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/?include=openingWindows");
        $openingWindows = $I->grabDataFromResponse('openingWindows.data');
        $this->assertPeriodEquals($I, $openingWindows[0], $this->makePeriod('2015-11-07 20:30:00', '2015-11-08 00:30:00'));
    }

    private function assertClosingAt(ApiTester $I, string $token, string $restaurantId, Carbon $expected)
    {
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId");
        $closingAt = new Carbon($I->grabDataFromResponse('closingAt'));
        $I->assertRoughlyEqual($closingAt, $expected);
    }

    private function assertPeriodEquals(ApiTester $I, $period, Period $expected)
    {
        $I->assertTrue($this->makePeriod($period['start'], $period['end'])->sameValueAs($expected));
    }

    private function makePeriod(string $start, string $end): Period
    {
        return new Period(new Carbon($start), new Carbon($end));
    }

    private function createOpeningWindow(string $restaurantId, string $start, string $end, bool $tomorrow = false)
    {
        $openingWindow = new OpeningWindow;
        $openingWindow->restaurantId = $restaurantId;
        $openingWindow->dayOfWeek = $tomorrow ? Carbon::now()->addDay()->dayOfWeek : Carbon::now()->dayOfWeek;
        $openingWindow->start = $start;
        $openingWindow->end = $end;
        $openingWindow->save();
    }

    private function createClosingWindow(string $restaurantId, string $start, string $end)
    {
        $closingWindow = new ClosingWindow;
        $closingWindow->restaurantId = $restaurantId;
        $closingWindow->start = new Carbon($start);
        $closingWindow->end = new Carbon($end);
        $closingWindow->save();
    }

    private function createRestaurant(): string
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

    private function isRestaurantInResponse(ApiTester $I, string $token, string $restaurantId, string $query = ''): bool
    {
        $I->sendApiGetWithToken($token, "restaurants?$query");
        $restaurants = $I->grabDataFromResponse();

        return !collect($restaurants)->filter(function ($restaurant) use ($restaurantId) {
            return $restaurant['id'] == $restaurantId;
        })->isEmpty();
    }
}
