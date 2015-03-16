<?php
namespace Groupeat\Auth\Handlers\Events;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Services\SendMail;
use Illuminate\Contracts\Routing\UrlGenerator;

class SendActivationLink
{
    private $mailer;
    private $urlGenerator;

    public function __construct(SendMail $mailer, UrlGenerator $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(UserHasRegistered $userHasRegistered)
    {
        $userCredentials = $userHasRegistered->getUser();
        $token = $this->generateActivationToken($userCredentials);
        $url = $this->urlGenerator->to("auth/activate?token=$token");

        $this->mailer->call(
            $userCredentials,
            'auth::activation',
            'auth::activation.subject',
            compact('url')
        );
    }

    private function generateActivationToken(UserCredentials $userCredentials, $length = 42)
    {
        // We generate twice as many bytes here because we want to ensure we have
        // enough after we base64 encode it to get the length we need because we
        // take out the "/", "+", and "=" characters.
        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false) {
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
