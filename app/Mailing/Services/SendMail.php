<?php
namespace Groupeat\Mailing\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Locale;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Psr\Log\LoggerInterface;

class SendMail
{
    private $mailer;
    private $locale;
    private $logger;

    public function __construct(
        Mailer $mailer,
        Locale $locale,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->logger = $logger;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function call(
        UserCredentials $user,
        string $viewName,
        string $subjectLangKey,
        array $data = []
    ) {
        $this->locale->executeWithUserLocale(function () use ($user, $viewName, $subjectLangKey, $data) {
            $email = $user->email;
            $viewFactory = $this->mailer->getViewFactory();
            $subject = $this->locale->getTranslator()->get($subjectLangKey);
            $text = $viewFactory->make("$viewName-text", $data)->render();
            $html = $viewFactory->make("$viewName-html", $data)->render();

            $this->mailer->send(
                ['raw' => $text],
                [],
                function (Message $message) use ($email, $subject, $html) {
                    $message->getSwiftMessage()->setBody($html, 'text/html');
                    $message->to($email)->subject($subject);
                }
            );

            $this->logger->info("The email [$viewName] has been sent to {$user->user->toShortString()}.");
        }, $user->locale);
    }
}
