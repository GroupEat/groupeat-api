<?php
namespace Groupeat\Notifications\Services\Abstracts;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Locale;

abstract class NotificationSender
{
    protected $locale;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    protected function translateFor($messageKey, UserCredentials $user)
    {
        return $this->locale->executeWithUserLocale(function () use ($messageKey) {
            return $this->locale->getTranslator()->get("notifications::messages.$messageKey");
        }, $user->locale);
    }
}
