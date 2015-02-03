<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Services\Locale;
use Illuminate\Mail\Mailer;
use Illuminate\Routing\UrlGenerator;

class SendActivationLink {

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var Locale
     */
    private $localeService;


    public function __construct(
        Mailer $mailer,
        UrlGenerator $urlGenerator,
        Locale $localeService
    )
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->localeService = $localeService;
    }

    /**
     * @param UserCredentials $userCredentials
     * @param string          $locale
     *
     * @return User
     */
    public function call(UserCredentials $userCredentials)
    {
        $view = 'auth::mails.activation';
        $token = $this->generateActivationToken($userCredentials);
        $email = $userCredentials->email;
        $url = $this->urlGenerator->route('auth.activate', compact('token'));
        $data = compact('url');

        $this->localeService->executeWithUserLocale(function() use ($view, $data, $email)
        {
            $this->mailer->send($view, $data, function($message) use ($email)
            {
                $subject = $this->localeService->getTranslator()
                    ->get('auth::activation.mail.subject');

                $message->to($email)->subject($subject);
            });
        }, $userCredentials->locale);
    }

    private function generateActivationToken(UserCredentials $userCredentials, $length = 42)
    {
        // We generate twice as many bytes here because we want to ensure we have
        // enough after we base64 encode it to get the length we need because we
        // take out the "/", "+", and "=" characters.
        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false)
        {
            throw new Exception(
                'cannotGenerateActivationToken',
                "Unable to generate a random string for the activation token."
            );
        }

        $token = substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        $userCredentials->activationToken = $token;
        $userCredentials->save();

        return $token;
    }

}
