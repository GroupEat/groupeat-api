<?php
namespace Groupeat\Auth\Handlers\Commands;

use Groupeat\Auth\Commands\SendPasswordResetLink;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Locale;
use Groupeat\Support\Values\AvailableLocales;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Routing\UrlGenerator;

class SendPasswordResetLinkHandler
{
    private $passwordBroker;
    private $localeService;
    private $urlGenerator;
    private $availableLocales;

    public function __construct(
        PasswordBroker $passwordBroker,
        Locale $localeService,
        UrlGenerator $urlGenerator,
        AvailableLocales $availableLocales
    ) {
        $this->passwordBroker = $passwordBroker;
        $this->localeService = $localeService;
        $this->urlGenerator = $urlGenerator;
        $this->availableLocales = $availableLocales;
    }

    public function handle(SendPasswordResetLink $command)
    {
        $email = $command->getEmail();
        $broker = $this->passwordBroker;
        $credentials = compact('email');

        $status = $this->localeService->executeWithUserLocale(function () use ($credentials) {
            return $this->passwordBroker->sendResetLink($credentials, function ($message, $user, $token) {
                $subject = $this->localeService->getTranslator()->get('auth::resetPassword.subject');
                $message->subject($subject);
            });
        }, $this->getLocaleFor($credentials));

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

    private function getLocaleFor(array $credentials)
    {
        $user = $this->passwordBroker->getUser($credentials);

        if (!empty($user)) {
            return $user->locale;
        }

        return $this->availableLocales[0];
    }
}
