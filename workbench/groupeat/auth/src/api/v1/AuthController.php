<?php namespace Groupeat\Auth\Api\V1;

use Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Api\V1\Controller;
use Input;

class AuthController extends Controller {

    public function getToken()
    {
        Auth::byCredentials(Input::get('email'), Input::get('password'));

        return $this->getTokenResponseFromUser(Auth::user());
    }

    public function resetToken()
    {
        $userCredentials = app('GenerateAuthTokenService')
            ->resetFromCredentials(Input::get('email'), Input::get('password'));

        return $this->getTokenResponseFromUser($userCredentials->user);
    }

    public function sendResetPasswordLink()
    {
        app('SendPasswordResetLinkService')->call(Input::get('email'));
    }

    /**
     * @param User $user
     *
     * @return \Dingo\Api\Http\ResponseBuilder
     */
    private function getTokenResponseFromUser(User $user)
    {
        $id = $user->id;
        $type = Auth::shortTypeOf($user);
        $token = $user->credentials->token;
        $activated = $user->credentials->isActivated();

        return $this->arrayResponse(compact('id', 'type', 'token', 'activated'));
    }

}
