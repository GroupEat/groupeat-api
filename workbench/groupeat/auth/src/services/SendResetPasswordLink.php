<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Illuminate\Mail\Mailer;
use Illuminate\Routing\UrlGenerator;

class SendResetPasswordLink {

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;


    public function __construct(Mailer $mailer, UrlGenerator $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param User $user
     */
    public function call(User $user, $email)
    {
//        $view = 'auth::mails.activation';
//        $code = $this->userCredentials->activationCode;
//        $url = $this->urlGenerator->route('auth.activation', compact('code'));
//        $data = compact('url');
//
//        $this->mailer->send($view, $data, function($message) use ($email)
//        {
//            // TODO: I18n.
//            $message->to($email)->subject("Activation de votre compte GroupEat");
//        });
    }

}
