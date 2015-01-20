<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Forbidden;
use Illuminate\Auth\Reminders\PasswordBroker;

class ResetPassword {

    /**
     * @var GenerateTokenForUser
     */
    private $authTokenGenerator;

    /**
     * @var PasswordBroker
     */
    private $passwordBroker;


    public function __construct(GenerateAuthToken $authTokenGenerator, PasswordBroker $passwordBroker)
    {
        $this->authTokenGenerator = $authTokenGenerator;
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
            $userCredentials->token = $this->authTokenGenerator->forUser($userCredentials);
            $userCredentials->setPassword($plainPassword);
            $userCredentials->save();
            dump('at reset: '.$userCredentials->token);
        });

        if ($status != $broker::PASSWORD_RESET)
        {
            throw new Forbidden($status);
        }
    }

}
