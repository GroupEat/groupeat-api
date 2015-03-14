<?php
namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Locale;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Routing\UrlGenerator;

class SendPasswordResetLink
{
    private $passwordBroker;
    private $localeService;
    private $urlGenerator;

    public function __construct(
        PasswordBroker $passwordBroker,
        Locale $localeService,
        UrlGenerator $urlGenerator
    ) {
        $this->passwordBroker = $passwordBroker;
        $this->localeService = $localeService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $email
     */
    public function call($email)
    {
        $broker = $this->passwordBroker;
        $credentials = compact('email');

        $status = $this->localeService->executeWithUserLocale(function () use ($broker, $credentials) {
            return $broker->sendResetLink($credentials, function ($message, $user, $token) {
                $subject = $this->localeService->getTranslator()->get('auth::resetPassword.subject');
                $message->subject($subject);
            });
        }, 'fr'); // TODO: Send the locale from the frontend

        if ($status == $broker::INVALID_USER) {
            UserCredentials::throwNotFoundByEmailException($email);
        }

        $userCredentials = $broker->getUser($credentials);

        $userCredentials->password = 'WAITING FOR PASSWORD RESET';
        $userCredentials->token = null;
        $userCredentials->save();
    }

    /**
     * @param $token
     *
     * @return string
     */
    public function getUrl($token)
    {
        return $this->urlGenerator->to("auth/password/reset?token=$token");
    }
}
