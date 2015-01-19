<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Services\Locale;
use Illuminate\Mail\Mailer;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as Validation;

class RegisterUser {

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var GenerateTokenForUser
     */
    private $tokenGenerator;

    /**
     * @var Locale
     */
    private $localeService;

    /**
     * @var UserCredentials
     */
    private $userCredentials;


    public function __construct(
        Mailer $mailer,
        Validation $validation,
        UrlGenerator $urlGenerator,
        GenerateTokenForUser $tokenGenerator,
        Locale $localeService
    )
    {
        $this->mailer = $mailer;
        $this->validation = $validation;
        $this->urlGenerator = $urlGenerator;
        $this->tokenGenerator = $tokenGenerator;
        $this->localeService = $localeService;
    }

    /**
     * @param string $email
     * @param string $plainPassword
     * @param string $locale
     * @param User   $userType
     *
     * @return User
     */
    public function call($email, $plainPassword, $locale, User $userType)
    {
        $newUser = $userType->newInstance();
        $this->localeService->assertAvailable($locale);

        $this->insertUserWithCredentials($email, $plainPassword, $locale, $newUser)
            ->generateActivationCode()
            ->sendActivationCode($email, $locale);

        return $this->userCredentials->user;
    }

    private function insertUserWithCredentials($email, $password, $locale, User $user)
    {
        $credentials = compact('email', 'password');

        $rules = [
            'email' => 'email|required|unique:'.UserCredentials::table(),
            'password' => 'min:6',
        ];

        $errors = $this->validation->make($credentials, $rules)->messages();

        if (!$errors->isEmpty())
        {
            // Delete the user if the credentials are invalid.
            $user->forceDelete();

            throw new BadRequest("Invalid credentials.", $errors);
        }

        // We need to save the user to have its id
        $user->save();

        $this->userCredentials = UserCredentials::register($email, $password, $locale, $user);
        $this->userCredentials->save();

        return $this;
    }

    private function generateActivationCode()
    {
        $this->userCredentials->activationToken = $this->generateRandomString();
        $this->userCredentials->save();

        return $this;
    }

    private function sendActivationCode($email, $locale)
    {
        $view = 'auth::mails.activation';
        $token = $this->userCredentials->activationToken;
        $url = $this->urlGenerator->route('auth.activate', compact('token'));
        $data = compact('url');

        $this->localeService->executeWithUserLocale(function() use ($view, $data, $email)
        {
            $this->mailer->send($view, $data, function($message) use ($email)
            {
                $subject = $this->localeService->getTranslator()->get('auth::activation.mail.subject');

                $message->to($email)->subject($subject);
            });
        }, $locale);

        return $this;
    }

    private function generateRandomString($length = 42)
    {
        // We generate twice as many bytes here because we want to ensure we have
        // enough after we base64 encode it to get the length we need because we
        // take out the "/", "+", and "=" characters.
        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false)
        {
            throw new Exception("Unable to generate random string.");
        }

        return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
    }

}
