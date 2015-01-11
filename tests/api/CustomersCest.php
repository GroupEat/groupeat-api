<?php

use Codeception\Module\ApiHelper;

class CustomersCest {

    public function testThatACustomerCanBeRegistered(ApiTester $I)
    {
        $I->sendApiPOST('customers', [
            'email' => 'derousseaux@ensta.fr',
            'password' => 'password',
        ]);

        $I->seeResponseCodeIs(201);
    }

}
