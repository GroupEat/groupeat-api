<?php
namespace Groupeat\Auth\Http\V1;

use Groupeat\Auth\Commands\ActivateUser;
use Groupeat\Auth\Commands\ChangePassword;
use Groupeat\Auth\Commands\ResetPassword;
use Groupeat\Auth\Commands\ResetToken;
use Groupeat\Auth\Commands\SendPasswordResetLink;
use Groupeat\Auth\Entities\UserCredentials;
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

        return $this->tokenResponseFor($this->auth->credentials());
    }

    public function resetToken()
    {
        return $this->tokenResponseFor($this->dispatch(new ResetToken(
            $this->json('email'),
            $this->json('password')
        )));
    }

    public function sendPasswordResetLink()
    {
        $this->dispatch(new SendPasswordResetLink($this->json('email')));
    }

    public function resetPassword()
    {
        return $this->tokenResponseFor($this->dispatch(new ResetPassword(
            $this->json('token'),
            $this->json('email'),
            $this->json('password')
        )));
    }

    public function changePassword()
    {
        return $this->tokenResponseFor($this->dispatch(new ChangePassword(
            $this->json('email'),
            $this->json('oldPassword'),
            $this->json('newPassword')
        )));
    }

    private function tokenResponseFor(UserCredentials $credentials)
    {
        return $this->itemResponse($credentials->user, new TokenTransformer);
    }
}
