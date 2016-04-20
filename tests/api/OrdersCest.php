<?php

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\ProductFormat;

class OrdersCest
{
    public function testThatACustomerShouldHaveNoMissingInformationToPlaceAnOrder(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();
        $orderDetails = $this->getOrderDetails($I, $token);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(403, 'missingCustomerInformation');

        list($token, $customerId) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $I->sendApiPutWithToken($token, "customers/$customerId", [
            'firstName' => 'Jean',
            'lastName' => 'Jacques',
            'phoneNumber' => '33605040302',
        ]);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);
    }

    public function testThatTheRestaurantReceiveAnEmailAndASmsWhenAnOrderIsPlaced(ApiTester $I)
    {
        list($token, $orderId, $restaurantCapacity, $orderDetails, $customerId) = $I->placeOrder();
        $productFormatId = array_keys($orderDetails['productFormats'])[0];
        $restaurantEmail = $this->getRestaurantEmailFromProductFormat($productFormatId);
        $I->assertSame('restaurants.orderHasBeenPlaced', $I->grabFirstMailId());
        $I->assertSame($restaurantEmail, $I->grabFirstMailRecipient());
        $I->grabFirstSms();
    }

    public function testThatAEndingAtTimeCanBeSpecified(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $endingAt = Carbon::now()->addHours(2);
        $orderDetails = $this->getOrderDetails($I, $token, ['endingAt' => $endingAt->toDateTimeString()]);
        unset($orderDetails['foodRushDurationInMinutes']);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);

        $orderId = $I->grabDataFromResponse('id');
        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $I->assertRoughlyEqual(new Carbon($I->grabDataFromResponse('groupOrder.data.endingAt')), $endingAt);

        $endingAtInAnAnotherOpenedWindow = Carbon::now()->addDays(2)->toDateTimeString();
        $orderDetails['endingAt'] = $endingAtInAnAnotherOpenedWindow;
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(400, 'invalidEndingAt');
    }

    public function testThatTheOrderCannotBeEmpty(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();

        $orderDetails = $this->getOrderDetails($I, $token, ['productFormats' => []]);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(400, 'missingProductFormats');

        $orderDetails = $this->getOrderDetails($I, $token, ['productFormats' => [1 => 0]]);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(400, 'missingProductFormats');
    }

    public function testThatTheProductFormatsMustExist(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $orderDetails = $this->getOrderDetails($I, $token, ['productFormats' => [66666 => 1]]);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(404, 'unexistingProductFormats');
    }

    public function testThatTheAnOrderCannotBePlacedOnAClosedRestaurant(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $now = Carbon::now();
        list($products) = $this->getProducts(
            $I,
            $token,
            ['around' => true, 'opened' => false],
            function ($restaurants) use ($now) {
                foreach ($restaurants as $restaurant) {
                    if (new Carbon($restaurant['closingAt']) == $now) {
                        return $restaurant['id'];
                    }
                }
            }
        );
        $productFormats = $this->getProductFormatsFrom($products);
        $orderDetails = $this->getOrderDetails($I, $token, compact('productFormats'));

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->dontSeeResponseCodeIs(201);
    }

    public function testThatTheRestaurantMustBeCloseEnough(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();

        list($products, $restaurantId) = $this->getProducts($I, $token);
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId?include=address");
        $address = $I->grabDataFromResponse('address.data');

        $productFormats = $this->getProductFormatsFrom($products);
        $orderDetails = $this->getOrderDetails($I, $token, [
            'productFormats' => $productFormats,
            'deliveryAddress' => [
                'latitude' => $address['latitude'] - 1,
                'longitude' => $address['longitude'] + 1,
            ],
        ]);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'deliveryDistanceTooLong');
    }

    public function testThatTheProductFormatsMustBelongToTheSameRestaurant(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $I->sendApiGetWithToken($token, 'restaurants');

        foreach ($I->grabDataFromResponse() as $restaurant) {
            if ($restaurant['name'] == 'Toujours fermé') {
                $wrongRestaurantId = $restaurant['id'];
                break;
            }
        }

        list($wrongProducts) = $this->getProducts(
            $I,
            $token,
            ['around' => true, 'opened' => true],
            function () use ($wrongRestaurantId) {
                return $wrongRestaurantId;
            }
        );

        $wrongProductFormats = $this->getProductFormatsFrom($wrongProducts);

        $orderDetails = $this->getOrderDetails($I, $token);

        $orderDetails['productFormats'] = $wrongProductFormats + $orderDetails['productFormats'];

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(400, 'productFormatsFromDifferentRestaurants');
    }

    public function testThatTheGroupOrderMustExceedTheMinimumPrice(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $options = ['around' => true, 'opened' => true];

        list($products, $restaurantId) = $this->getProducts($I, $token, $options);
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId");
        $minimumPrice = $I->grabDataFromResponse('minimumGroupOrderPrice');

        $price = 0;
        $productFormats = [];

        foreach ($products as $product) {
            foreach ($product['formats']['data'] as $format) {
                if (($price + $format['price']) < $minimumPrice) {
                    $price += $format['price'];
                    $productFormats[$format['id']] = 1;
                }
            }
        }

        $orderDetails = $this->getOrderDetails($I, $token, compact('productFormats'));
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'minimumGroupOrderPriceNotReached');
    }

    public function testThatTheOrderShouldNotExceedRestaurantDeliveryCapacity(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $options = ['around' => true, 'opened' => true];
        $deliveryCapacity = 0;

        list($products, $restaurantId) = $this->getProducts($I, $token, $options);
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId");
        $deliveryCapacity = $I->grabDataFromResponse('deliveryCapacity');

        $quantity = 0;
        $productFormats = [];

        foreach ($products as $product) {
            foreach ($product['formats']['data'] as $format) {
                if ($quantity <= $deliveryCapacity) {
                    $quantity++;
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
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $orderDetails = $this->getOrderDetails($I, $token);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);

        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeErrorResponse(422, 'groupOrderAlreadyExisting');
    }

    public function testThatGroupOrdersCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $I->sendApiGetWithToken($token, 'groupOrders');
        $I->seeResponseCodeIs(200);
    }

    public function testThatAnOrderCanBeShownByTheCustomerWhoPlacedItOnly(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $orderDetails = $this->getOrderDetails($I, $token);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $I->grabDataFromResponse('id');
        $I->sendApiGetWithToken($token, "orders/$orderId");
        $I->seeResponseCodeIs(200);
        $I->assertSame($orderId, $I->grabDataFromResponse('id'));

        list($token2) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $I->sendApiGetWithToken($token2, "orders/$orderId");
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');
    }

    public function testThatTheDiscountRateIncreaseWhenAGroupOrderIsJoined(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
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
        $I->sendApiPostWithToken($token, "groupOrders/$groupOrderId/orders", $orderDetails);
        $I->seeResponseCodeIs(201);
        $newDiscountRate = $this->computeDiscountRate(
            $I->grabDataFromResponse('discountedPrice'),
            $I->grabDataFromResponse('rawPrice')
        );
        $I->sendApiGetWithToken($token, "groupOrders/$groupOrderId");
        $newDiscountRateFormGroupOrder = $I->grabDataFromResponse('discountRate');

        $I->assertGreaterThan($oldDiscountRate, $newDiscountRate);
        $I->assertSame($newDiscountRate, $newDiscountRateFormGroupOrder);

        $I->sendApiGetWithToken($token, "orders/$orderId");
        $newRawPrice = $I->grabDataFromResponse('rawPrice');
        $I->assertSame($oldRawPrice, $newRawPrice);
        $newDiscountRateFromOrder = $this->computeDiscountRate(
            $I->grabDataFromResponse('discountedPrice'),
            $newRawPrice
        );
        $I->assertSame($newDiscountRate, $newDiscountRateFromOrder);
    }

    public function testThatTheDeliveryAddressMustBeCloseEnoughToJoinAGroupOrder(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $orderDetails = $this->getOrderDetails($I, $token);
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $orderId = $I->grabDataFromResponse('id');
        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $groupOrderId = $I->grabDataFromResponse('groupOrder.data.id');

        $orderDetails['groupOrderId'] = $groupOrderId;
        $orderDetails['deliveryAddress']['latitude']++;
        unset($orderDetails['foodRushDurationInMinutes']);

        $I->sendApiPostWithToken($token, "groupOrders/$groupOrderId/orders", $orderDetails);
        $I->seeErrorResponse(422, 'deliveryDistanceTooLong');
    }

    public function testThatTheRestaurantCanSeeIfTheCustomerAttachedACommentToItsOrder(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomerWithNoMissingInformation();
        $orderDetails = $this->getOrderDetails($I, $token);
        $comment = "Please add some meat to my vegan pizza...";
        $orderDetails['comment'] = $comment;
        $I->sendApiPostWithToken($token, 'orders', $orderDetails);
        $I->seeResponseCodeIs(201);
        $I->assertSame($comment, $I->grabDataFromResponse('comment'));
        $I->assertFirstMailContains($comment);
    }

    public function testThatACustomerCanListItsOrders(ApiTester $I)
    {
        list($token1, $orderId1,,, $customerId1) = $I->placeOrder();
        list($token2, $orderId2,,, $customerId2) = $I->placeOrder();

        $I->sendApiGetWithToken($token1, "customers/$customerId1/orders");
        $orders1 = $I->grabDataFromResponse('');
        $found = false;

        foreach ($orders1 as $order1) {
            $I->assertNotEquals($orderId2, $order1['id']);

            if ($order1['id'] == $orderId1) {
                $found = true;
            }
        }

        $I->assertSame(true, $found);
    }

    public function testThatACustomerCanListItsOrdersInASpecificGroupOrder(ApiTester $I)
    {
        list($token, $orderId,,, $customerId) = $I->placeOrder();

        $I->sendApiGetWithToken($token, "orders/$orderId?include=groupOrder");
        $groupOrderId = $I->grabDataFromResponse('groupOrder.data.id');

        $I->sendApiGetWithToken($token, "customers/$customerId/groupOrders/$groupOrderId/orders");
        $orders = $I->grabDataFromResponse('');
        $found = false;

        foreach ($orders as $order) {
            if ($order['id'] == $orderId) {
                $found = true;
            }
        }

        $I->assertSame(true, $found);
    }

    private function getProducts(
        ApiTester $I,
        string $token,
        array $options = ['around' => true, 'opened' => true],
        Closure $getRestaurantIdCallback = null
    ): array {
        if (is_null($getRestaurantIdCallback)) {
            $getRestaurantIdCallback = function ($restaurants) {
                return collect($restaurants)->sortByDesc(function ($restaurant) {
                    return Carbon::createFromFormat(
                        Carbon::DEFAULT_TO_STRING_FORMAT,
                        $restaurant['closingAt']
                    );
                })->first()['id'];
            };
        }

        $restaurantsUrl = 'restaurants?'.http_build_query($this->getQueryStringParamsFor($options));
        $I->sendApiGetWithToken($token, $restaurantsUrl);
        $restaurantId = $getRestaurantIdCallback($I->grabDataFromResponse());
        $I->sendApiGetWithToken($token, "restaurants/$restaurantId/products?include=formats");

        return [$I->grabDataFromResponse(), $restaurantId];
    }

    private function getOrderDetails(ApiTester $I, string $token, array $details = []): array
    {
        $defaultProductFormats = '{}';

        if (empty($details['productFormats'])) {
            list($products) = $this->getProducts($I, $token);
            $defaultProductFormats = $this->getProductFormatsFrom($products);
        }

        $details = array_merge([
            'foodRushDurationInMinutes' => 30,
            'productFormats' => $defaultProductFormats,
            'deliveryAddress' => [
                'street' => "Allée des techniques avancées",
                'details' => "Bâtiment A, chambre 200",
                'latitude' => $this->getDefaultLatitude(),
                'longitude' => $this->getDefaultLongitude(),
            ],
        ], $details);

        return $details;
    }

    private function getQueryStringParamsFor(array $options = []): array
    {
        $latitude = isset($options['latitude']) ? $options['latitude'] : $this->getDefaultLatitude();
        $longitude = isset($options['longitude']) ? $options['longitude'] : $this->getDefaultLongitude();

        $queryStringParams = [];

        if (!empty($options['around'])) {
            $queryStringParams['include'][] = 'address';
            $queryStringParams['around'] = true;
            $queryStringParams['latitude'] = $latitude;
            $queryStringParams['longitude'] = $longitude;
        }

        if (!empty($options['opened'])) {
            $queryStringParams['opened'] = true;
        }

        $queryStringParams['include'] = implode(',', $queryStringParams['include']);

        return $queryStringParams;
    }

    private function getProductFormatsFrom(array $products, array $selectedProducts = [[0, 0, 2], [1, 2, 1]]): array
    {
        $productFormats = [];

        foreach ($selectedProducts as $selectedProduct) {
            $productFormats[$products[$selectedProduct[0]]['formats']['data'][$selectedProduct[1]]['id']]
                = $selectedProduct[2];
        }

        return $productFormats;
    }

    private function getDefaultLatitude(): float
    {
        return 48.716941;
    }

    private function getDefaultLongitude(): float
    {
        return 2.239171;
    }

    private function getRestaurantEmailFromProductFormat(string $productFormatId): string
    {
        return ProductFormat::find($productFormatId)->product->restaurant->email;
    }

    private function computeDiscountRate($discountedPrice, $rawPrice): int
    {
        return round(100 * (1 - $discountedPrice / $rawPrice));
    }
}
