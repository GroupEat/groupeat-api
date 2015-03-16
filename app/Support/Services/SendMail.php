<?php
namespace Groupeat\Support\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Illuminate\Contracts\Mail\Mailer;

class SendMail
{
    private $mailer;
    private $locale;

    public function __construct(Mailer $mailer, Locale $locale)
    {
        $this->mailer = $mailer;
        $this->locale = $locale;
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
     * @param string          $view
     * @param string          $subjectLangKey
     * @param array           $data
     */
    public function call(UserCredentials $user, $view, $subjectLangKey, array $data = [])
    {
        $this->locale->executeWithUserLocale(function () use ($user, $view, $subjectLangKey, $data) {
            $views = ["$view-html", "$view-text"];

            $this->mailer->send(
                $views,
                $data,
                function ($message) use ($user, $subjectLangKey) {
                    $subject = $this->locale->getTranslator()->get($subjectLangKey);

                    $message->to($user->email)->subject($subject);
                }
            );
        }, $user->locale);
    }
}
