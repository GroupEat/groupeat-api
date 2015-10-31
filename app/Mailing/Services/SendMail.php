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

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param UserCredentials $user
     * @param string          $viewName
     * @param string          $subjectLangKey
     * @param array           $data
     */
    public function call(UserCredentials $user, $viewName, $subjectLangKey, array $data = [])
    {
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