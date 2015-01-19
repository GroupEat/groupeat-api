<?php

class CustomerAddressesCest {

    public function testThatACustomerCanUpdateAndGetItsAddress(ApiTester $I)
    {
        list($token, $id) = $I->sendRegistrationRequest();

        $address = [
            'street' => "Allée des techniques avancées",
            'details' => "Bâtiment A, chambre 200",
            'latitude' => 48.711042,
            'longitude' => 2.219278,
        ];

        $I->sendApiPutWithToken($token, "customers/$id/address", $address);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsData($address);

        $I->sendApiGetWithToken($token, "customers/$id/address");
        $I->seeResponseCodeIs(200);

        // We cannot check the latitude or longitude for strict equality
        // because of troncation errors.
        $I->seeResponseContainsData(['street' => "Allée des techniques avancées"]);
    }

}
