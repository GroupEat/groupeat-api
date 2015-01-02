<?php namespace Groupeat\Auth\Api\V1;

use App;
use Groupeat\Support\Api\V1\Controller;
use Input;

class AuthController extends Controller {

    public function token()
    {
        $token = App::make('GenerateTokenForUserService')
            ->call(Input::get('email'), Input::get('password'));

        return $this->response->array(compact('token'));
    }

}
