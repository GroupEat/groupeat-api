<?php
namespace Groupeat\Support\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Illuminate\Mail\Mailer;

class SendMail
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Locale
     */
    private $localeService;

    public function __construct(Mailer $mailer, Locale $localeService)
    {
        $this->mailer = $mailer;
        $this->localeService = $localeService;
    }

    /**
     * @return Locale
     */
    public function getLocaleService()
    {
        return $this->localeService;
    }

    /**
     * @param UserCredentials $user
     * @param string          $view
     * @param string          $subjectLangKey
     * @param array           $data
     */
    public function call(UserCredentials $user, $view, $subjectLangKey, array $data = [])
    {
        $this->localeService->executeWithUserLocale(function () use ($user, $view, $subjectLangKey, $data) {
            $views = ["$view-html", "$view-text"];

            $this->mailer->send(
                $views,
                $data,
                function ($message) use ($user, $subjectLangKey) {
                    $subject = $this->localeService->getTranslator()->get($subjectLangKey);

                    $message->to('tib.dex@gmail.com')->subject($subject);
                }
            );
        }, $user->locale);
    }
}
