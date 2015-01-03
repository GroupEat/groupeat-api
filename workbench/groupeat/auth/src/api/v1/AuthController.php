<?php namespace Groupeat\Auth\Api\V1;

use App;
use Auth;
use Groupeat\Support\Api\V1\Controller;
use Input;

class AuthController extends Controller {

    public function token()
    {
        $user = App::make('GenerateTokenForUserService')
            ->call(Input::get('email'), Input::get('password'));

        $id = $user->id;
        $type = Auth::shortTypeOf($user);
        $token = $user->credentials->token;

        return $this->response->array(compact('id', 'type', 'token'));
    }

}
