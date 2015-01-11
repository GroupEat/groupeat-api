<?php

use Codeception\Module\ApiHelper;

class AuthCest {

    public function testThatPassingTokenInTheQueryStringIsForbidden(ApiTester $I)
    {
        $a = 1;
        $I->sendApiGET('auth/token?token=shouldBePassedInHeader');
        $I->seeErrorResponse(403, "Trying to authenticate via token in query string is forbidden.");
    }

}
