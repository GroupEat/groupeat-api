<?php
namespace Groupeat\Auth\Http\V1;

use Groupeat\Auth\Jobs\ChangePassword;
use Groupeat\Auth\Jobs\ResetPassword;
use Groupeat\Auth\Jobs\ResetToken;
use Groupeat\Auth\Jobs\SendPasswordResetLink;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Events\UserHasRetrievedItsToken;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Illuminate\Contracts\Events\Dispatcher;

class AuthController extends Controller
{
    public function getToken(Dispatcher $events)
    {
        $this->auth->byCredentials($this->json('email'), $this->json('password'));

        $events->fire(new UserHasRetrievedItsToken($this->auth->user()));

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
        return $this->itemResponse($credentials->user, new UserTransformer);
    }
}
