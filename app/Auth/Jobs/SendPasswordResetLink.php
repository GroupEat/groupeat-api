<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Jobs\Abstracts\Job;
use Groupeat\Support\Services\Locale;
use Groupeat\Support\Values\AvailableLocales;
use Illuminate\Contracts\Auth\PasswordBroker;

class SendPasswordResetLink extends Job
{
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function handle(PasswordBroker $broker, Locale $locale, AvailableLocales $availableLocales)
    {
        $email = $this->email;
        $credentials = compact('email');

        $status = $locale->executeWithUserLocale(function () use ($credentials, $broker, $locale) {
            return $broker->sendResetLink($credentials, function ($message, $user, $token) use ($locale) {
                $subject = $locale->getTranslator()->get('auth::resetPassword.subject');
                $message->subject($subject);
            });
        }, $this->getLocaleFor($availableLocales, $broker->getUser($credentials)));

        if ($status == $broker::INVALID_USER) {
            UserCredentials::throwNotFoundByEmailException($email);
        }
    }

    private function getLocaleFor(AvailableLocales $availableLocales, $user)
    {
        if (!empty($user)) {
            return $user->locale;
        }

        return $availableLocales->value()[0];
    }
}
