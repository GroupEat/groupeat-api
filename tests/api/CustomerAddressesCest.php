<?php

class CustomerAddressesCest
{
    public function testThatACustomerCanUpdateAndGetItsAddress(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $address = [
            'street' => "Allée des techniques avancées",
            'details' => "Bâtiment A, chambre 200",
            'city' => "Palaiseau",
            'postcode' => "91120",
            'state' => "Essonne",
            'country' => "France",
            'latitude' => 48.716941,
            'longitude' => 2.239171,
        ];

        $I->sendApiPutWithToken($token, "customers/$id/address", $address);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsData($address);

        $I->sendApiGetWithToken($token, "customers/$id/address");
        $I->seeResponseCodeIs(200);

        // We cannot check the latitude or longitude for strict equality
        // because of truncation errors.
        $I->seeResponseContainsData(['street' => "Allée des techniques avancées"]);
    }

    public function testThatThePredefinedAddressesCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, "predefinedAddresses");
        $I->seeResponseCodeIs(200);
    }

    public function testThatANotFoundErrorIsReturnedWhenTheCustomerDoesNotHaveAnAddress(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();
        $I->sendApiGetWithToken($token, "customers/$id/address");
        $I->seeErrorResponse(404, 'noAddressForThisCustomer');
    }
}
