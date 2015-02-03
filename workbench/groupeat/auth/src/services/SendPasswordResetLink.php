<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Locale;
use Illuminate\Auth\Reminders\PasswordBroker;

class SendPasswordResetLink {

    /**
     * @var PasswordBroker
     */
    private $passwordBroker;

    /**
     * @var Locale
     */
    private $localeService;


    public function __construct(PasswordBroker $passwordBroker, Locale $localeService)
    {
        $this->passwordBroker = $passwordBroker;
        $this->localeService = $localeService;
    }

    /**
     * @param string $email
     */
    public function call($email)
    {
        $broker = $this->passwordBroker;
        $credentials = compact('email');

        $status = $this->localeService->executeWithUserLocale(function() use ($broker, $credentials)
        {
            return $broker->remind($credentials, function($message, $user, $token)
            {
                $subject = $this->localeService->getTranslator()->get('auth::resetPassword.mail.subject');

                $message->subject($subject);
            });
        });

        if ($status == $broker::INVALID_USER)
        {
            UserCredentials::throwNotFoundByEmailException($email);
        }

        $userCredentials = $broker->getUser($credentials);

        $userCredentials->password = 'WAITING FOR PASSWORD RESET';
        $userCredentials->token = null;
        $userCredentials->save();
    }

}
