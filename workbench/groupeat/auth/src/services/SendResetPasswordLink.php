<?php namespace Groupeat\Auth\Services;

use Groupeat\Support\Exceptions\NotFound;
use Illuminate\Auth\Reminders\PasswordBroker;

class SendResetPasswordLink {

    /**
     * @var PasswordBroker
     */
    private $passwordBroker;


    public function __construct(PasswordBroker $passwordBroker)
    {
        $this->passwordBroker = $passwordBroker;
    }

    /**
     * @param string $email
     */
    public function call($email)
    {
        $broker = $this->passwordBroker;
        $credentials = compact('email');
        $status = $broker->remind($credentials);

        if ($status == $broker::INVALID_USER)
        {
            throw new NotFound($status);
        }

        $userCredentials = $broker->getUser($credentials);

        $userCredentials->token = 'obsolete';
        $userCredentials->save();
    }

}
