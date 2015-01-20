<?php namespace Groupeat\Auth\Api\V1;

use App;
use Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Api\V1\Controller;
use Groupeat\Support\Exceptions\Unauthorized;
use Input;

class AuthController extends Controller {

    public function getToken()
    {
        Auth::attemptByCredentials(Input::get('email'), Input::get('password'));

        if (!Auth::check())
        {
            throw new Unauthorized("Bad credentials.");
        }

        return $this->getTokenResponseFromUser(Auth::user());
    }

    public function resetToken()
    {
        $userCredentials = App::make('GenerateAuthTokenService')
            ->resetFromCredentials(Input::get('email'), Input::get('password'));

        return $this->getTokenResponseFromUser($userCredentials->user);
    }

    public function sendResetPasswordLink()
    {
        App::make('SendResetPasswordLinkService')->call(Input::get('email'));
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
