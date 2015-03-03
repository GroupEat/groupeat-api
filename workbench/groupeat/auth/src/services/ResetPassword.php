<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Auth\Reminders\PasswordBroker;
use Illuminate\Translation\Translator;

class ResetPassword {

    /**
     * @var GenerateTokenForUser
     */
    private $authTokenGenerator;

    /**
     * @var PasswordBroker
     */
    private $passwordBroker;

    /**
     * @var Translator
     */
    private $translator;


    public function __construct(
        GenerateAuthToken $authTokenGenerator,
        PasswordBroker $passwordBroker,
        Translator $translator
    )
    {
        $this->authTokenGenerator = $authTokenGenerator;
        $this->passwordBroker = $passwordBroker;
        $this->translator = $translator;
    }

    /**
     * @param string $token
     * @param string $email
     * @param string $password
     */
    public function call($token, $email, $password)
    {
        $broker = $this->passwordBroker;
        $password_confirmation = $password;
        $credentials = compact('token', 'email', 'password', 'password_confirmation');

        $status = $broker->reset($credentials, function(UserCredentials $userCredentials, $plainPassword)
        {
            $userCredentials->resetPassword($plainPassword, $this->authTokenGenerator->forUser($userCredentials));
        });

        $message = $this->translator->get("auth::resetPassword.$status");

        switch ($status)
        {
            case $broker::INVALID_USER:
                throw new NotFound(
                    'noUserForPasswordResetToken',
                    $message
                );

            case $broker::INVALID_PASSWORD:
                throw new UnprocessableEntity(
                    'badPassword',
                    $message
                );

            case $broker::INVALID_TOKEN:
                throw new Forbidden(
                    'invalidPasswordResetToken',
                    $message
                );
        }
    }

}
