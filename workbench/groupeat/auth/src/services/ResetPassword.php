<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Forbidden;
use Illuminate\Auth\Reminders\PasswordBroker;

class ResetPassword {

    /**
     * @var PasswordBroker
     */
    private $passwordBroker;


    public function __construct(PasswordBroker $passwordBroker)
    {
        $this->passwordBroker = $passwordBroker;
    }

    /**
     * @param string $token
     * @param string $email
     * @param string $password
     * @param string $password_confirmation
     */
    public function call($token, $email, $password, $password_confirmation)
    {
        $broker = $this->passwordBroker;
        $credentials = compact('token', 'email', 'password', 'password_confirmation');

        $status = $broker->reset($credentials, function(UserCredentials $userCredentials, $plainPassword)
        {
            $userCredentials->resetPassword($plainPassword)->save();
        });

        if ($status != $broker::PASSWORD_RESET)
        {
            throw new Forbidden($status);
        }
    }

}
