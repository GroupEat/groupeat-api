<?php namespace Groupeat\Support\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Locale;
use Illuminate\Mail\Mailer;

class SendMail {

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
    public function call(UserCredentials $user, $view, $subjectLangKey, array $data)
    {
        $this->localeService->executeWithUserLocale(function() use ($user, $view, $subjectLangKey, $data)
        {
            $this->mailer->send($view, $data, function($message) use ($user, $subjectLangKey)
            {
                $subject = $this->localeService->getTranslator()->get($subjectLangKey);

                $message->to($user->email)->subject($subject);
            });
        }, $user->locale);
    }

}
