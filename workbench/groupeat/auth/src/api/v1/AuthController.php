<?php namespace Groupeat\Auth\Api\V1;

use App;
use Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Api\V1\Controller;
use Input;

class AuthController extends Controller {

    public function refreshToken()
    {
        $user = App::make('GenerateTokenForUserService')
            ->call(Input::get('email'), Input::get('password'));

        $id = $user->id;
        $type = Auth::shortTypeOf($user);
        $token = $user->credentials->token;

        return $this->arrayResponse(compact('id', 'type', 'token'));
    }

    public function sendResetPasswordLink()
    {
        App::make('SendResetPasswordLinkService')->call(Input::get('email'));
    }

}
