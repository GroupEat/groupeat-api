<?php
namespace Groupeat\Auth\Http\V1;

use Groupeat\Auth\Services\ActivateUser;
use Groupeat\Auth\Services\GenerateAuthToken;
use Groupeat\Auth\Services\ResetPassword;
use Groupeat\Auth\Services\SendPasswordResetLink;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class AuthController extends Controller
{
    public function activate(ActivateUser $activateUser)
    {
        $activateUser->call($this->json('token'));
    }

    public function getToken()
    {
        $this->auth->byCredentials($this->json('email'), $this->json('password'));

        return $this->itemResponse($this->auth->user(), new TokenTransformer);
    }

    public function resetToken(GenerateAuthToken $generateAuthToken)
    {
        $userCredentials = $generateAuthToken->resetFromCredentials(
            $this->json('email'),
            $this->json('password')
        );

        return $this->itemResponse($userCredentials->user, new TokenTransformer);
    }

    public function sendPasswordResetLink(SendPasswordResetLink $sendPasswordResetLink)
    {
        $sendPasswordResetLink->call($this->json('email'));
    }

    public function resetPassword(ResetPassword $resetPassword)
    {
        $resetPassword->call(
            $this->json('token'),
            $this->json('email'),
            $this->json('password')
        );
    }
}
