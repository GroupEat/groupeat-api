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

    protected function translateFor($messageKey, UserCredentials $user, array $params = [])
    {
        return $this->locale->executeWithUserLocale(function () use ($messageKey, $params) {
            return $this->locale->getTranslator()->get("notifications::messages.$messageKey", $params);
        }, $user->locale);
    }
}
