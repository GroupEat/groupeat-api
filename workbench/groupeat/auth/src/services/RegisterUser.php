<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Services\Traits\PerformValidation;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Lang;

class RegisterUser {

    use PerformValidation;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var UserCredentials
     */
    private $userCredentials;


    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function call($email, $plainPassword, User $user)
    {
        return $this->insertUserWithCredentials($email, $plainPassword, $user)
                && $this->generateActivationCode()
                && $this->sendActivationCode($email);
    }

    private function insertUserWithCredentials($email, $plainPassword, User $user)
    {
        // We need to save the user to have its id
        $user->save();

        $this->userCredentials = new UserCredentials;
        $this->userCredentials->email = $email;
        $this->userCredentials->password = $plainPassword;
        $this->userCredentials->user = $user;

        $fields = [
            'email' => $email,
            'password' => $plainPassword,
        ];

        $rules = [
            'email' => 'email|required|unique:'.UserCredentials::table(),
            'password' => 'min:6',
        ];

        if (!$this->checkRules($fields, $rules))
        {
            // Delete the user if the credentials are invalid
            $user->delete();

            return false;
        }

        return $this->userCredentials->save();
    }

    private function generateActivationCode()
    {
        $this->userCredentials->activationCode = $this->generateRandomString();

        return $this->userCredentials->save();
    }

    private function sendActivationCode($email)
    {
        $view = 'auth::mails.activation';
        $data = ['activationCode' => $this->userCredentials->activationCode];

        $this->mailer->send($view, $data, function(Message $message) use ($email)
        {
            $message->to($email)->subject(Lang::get('auth::activation.mail_subject'));
        });

        return true;
    }

    private function generateRandomString($length = 42)
    {
        // We generate twice as many bytes here because we want to ensure we have
        // enough after we base64 encode it to get the length we need because we
        // take out the "/", "+", and "=" characters.
        $bytes = openssl_random_pseudo_bytes($length * 2);

        // Stop execution if the generation fails
        if ($bytes === false)
        {
            throw new \RuntimeException('Unable to generate random string.');
        }

        return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
    }

}
