<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Mail\Mailer;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Factory as Validation;
use RuntimeException;

class RegisterUser {

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var GenerateTokenForUser
     */
    private $tokenGenerator;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

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
        GenerateTokenForUser $tokenGenerator
    )
    {
        $this->mailer = $mailer;
        $this->validation = $validation;
        $this->urlGenerator = $urlGenerator;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param string $email
     * @param string $plainPassword
     * @param User   $newUser
     *
     * @return User
     */
    public function call($email, $plainPassword, User $newUser)
    {
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
        else
        {
            $user->forceSave();
        }

        $this->userCredentials->save();

        return $this;
    }

    private function generateActivationCode()
    {
        $this->userCredentials->activationCode = $this->generateRandomString();
        $this->userCredentials->save();

        return $this;
    }

    private function sendActivationCode($email)
    {
        $view = 'auth::mails.activation';
        $code = $this->userCredentials->activationCode;
        $url = $this->urlGenerator->route('auth.activation', compact('code'));
        $data = compact('url');

        $this->mailer->send($view, $data, function($message) use ($email)
        {
            // TODO: I18n.
            $message->to($email)->subject("Activation de votre compte GroupEat");
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
            throw new RuntimeException("Unable to generate random string.");
        }

        return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
    }

}
