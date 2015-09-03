<?php
namespace Groupeat\Notifications\Services\Abstracts;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Locale;
use Groupeat\Support\Values\Environment;
use Psr\Log\LoggerInterface;

abstract class NotificationSender
{
    protected $locale;
    protected $logger;

    public function __construct(Locale $locale, LoggerInterface $logger)
    {
        $this->locale = $locale;
        $this->logger = $logger;
    }

    protected function translateFor($messageKey, UserCredentials $user)
    {
        return $this->locale->executeWithUserLocale(function () use ($messageKey) {
            return $this->locale->getTranslator()->get("notifications::messages.$messageKey");
        }, $user->locale);
    }
}
