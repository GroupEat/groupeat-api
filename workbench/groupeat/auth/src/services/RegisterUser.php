<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Exception;
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
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var GenerateTokenForUser
     */
    private $tokenGenerator;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var UserCredentials
     */
    private $userCredentials;


    public function __construct(
        Mailer $mailer,
        Validation $validation,
        UrlGenerator $urlGenerator,
        GenerateTokenForUser $tokenGenerator,
        Translator $translator
    )
    {
        $this->mailer = $mailer;
        $this->validation = $validation;
        $this->urlGenerator = $urlGenerator;
        $this->tokenGenerator = $tokenGenerator;
        $this->translator = $translator;
    }

    /**
     * @param string $email
     * @param string $plainPassword
     * @param User   $userType
     *
     * @return User
     */
    public function call($email, $plainPassword, User $userType)
    {
        $newUser = $userType->newInstance();

        $this->insertUserWithCredentials($email, $plainPassword, $newUser)
            ->generateActivationCode()
            ->sendActivationCode($email);

        return $this->userCredentials->user;
    }

    private function insertUserWithCredentials($email, $password, User $user)
    {
        // We need to save the user to have its id
        $user->forceSave();

        $this->userCredentials = new UserCredentials;
        $this->userCredentials->email = $email;
        $this->userCredentials->password = $password;
        $this->userCredentials->user = $user;

        $data = compact('email', 'password');

        $rules = [
            'email' => 'email|required|unique:'.UserCredentials::table(),
            'password' => 'min:6',
        ];

        $errors = $this->validation->make($data, $rules)->messages();

        if (!$errors->isEmpty())
        {
            // Delete the user if the credentials are invalid.
            $user->forceDelete();

            throw new BadRequest("Invalid credentials.", $errors);
        }

        $this->userCredentials->save();

        return $this;
    }

    private function generateActivationCode()
    {
        $this->userCredentials->activationToken = $this->generateRandomString();
        $this->userCredentials->save();

        return $this;
    }

    private function sendActivationCode($email)
    {
        $view = 'auth::mails.activation';
        $token = $this->userCredentials->activationToken;
        $url = $this->urlGenerator->route('auth.activate', compact('token'));
        $data = compact('url');

        $this->mailer->send($view, $data, function($message) use ($email)
        {
            $message->to($email)->subject($this->translator->get('auth::activation.mail.subject'));
        });

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
