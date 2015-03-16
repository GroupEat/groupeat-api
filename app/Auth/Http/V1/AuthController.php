<?php
namespace Groupeat\Auth\Http\V1;

use Groupeat\Auth\Commands\ActivateUser;
use Groupeat\Auth\Commands\ResetPassword;
use Groupeat\Auth\Commands\ResetToken;
use Groupeat\Auth\Commands\SendPasswordResetLink;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class AuthController extends Controller
{
    public function activate()
    {
        $this->dispatch(new ActivateUser($this->json('token')));
    }

    public function getToken()
    {
        $this->auth->byCredentials($this->json('email'), $this->json('password'));

        return $this->itemResponse($this->auth->user(), new TokenTransformer);
    }

    public function resetToken()
    {
        $userCredentials = $this->dispatch(new ResetToken(
            $this->json('email'),
            $this->json('password')
        ));

        return $this->itemResponse($userCredentials->user, new TokenTransformer);
    }

    public function sendPasswordResetLink()
    {
        $this->dispatch(new SendPasswordResetLink($this->json('email')));
    }

    public function resetPassword()
    {
        $this->dispatch(new ResetPassword(
            $this->json('token'),
            $this->json('email'),
            $this->json('password')
        ));
    }
}
