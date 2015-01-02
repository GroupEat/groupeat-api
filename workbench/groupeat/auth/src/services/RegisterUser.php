<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Mail\Mailer;
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
     * @var Validation
     */
    private $validation;

    /**
     * @var UserCredentials
     */
    private $userCredentials;


    public function __construct(Mailer $mailer, Validation $validation, GenerateTokenForUser $tokenGenerator)
    {
        $this->mailer = $mailer;
        $this->validation = $validation;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param string $email
     * @param string $plainPassword
     * @param User   $user
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

    private function insertUserWithCredentials($email, $plainPassword, User $user)
    {
        // We need to save the user to have its id
        // A user cannot be saved with empty data so we create temporary one
        $user->firstName = 'temp';
        $user->save();

        $this->userCredentials = new UserCredentials;
        $this->userCredentials->email = $email;
        $this->userCredentials->password = $plainPassword;
        $this->userCredentials->user = $user;

        $data = [
            'email' => $email,
            'password' => $plainPassword,
        ];

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
            $user->firstName = null;
            $user->save();
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
        $data = ['activationCode' => $this->userCredentials->activationCode];

        $this->mailer->send($view, $data, function($message) use ($email)
        {
            // TODO: I18n.
            $message->to($email)->subject("Activate your GroupEat account");
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
