<?php
namespace Groupeat\Auth\Http\V1;

use Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Input;

class AuthController extends Controller
{
    public function activate()
    {
        app('ActivateUserService')->call(Input::json('token'));
    }

    public function getToken()
    {
        Auth::byCredentials(Input::json('email'), Input::json('password'));

        return $this->itemResponse(Auth::user(), new TokenTransformer);
    }

    public function resetToken()
    {
        $userCredentials = app('GenerateAuthTokenService')
            ->resetFromCredentials(Input::json('email'), Input::json('password'));

        return $this->itemResponse($userCredentials->user, new TokenTransformer);
    }

    public function sendPasswordResetLink()
    {
        app('SendPasswordResetLinkService')->call(Input::json('email'));
    }

    public function resetPassword()
    {
        app('ResetPasswordService')->call(
            Input::json('token'),
            Input::json('email'),
            Input::json('password')
        );
    }
}
