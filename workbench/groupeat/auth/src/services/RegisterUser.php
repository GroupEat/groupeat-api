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
     * @var Validation
     */
    private $validation;

    /**
     * @var SendActivationLink
     */
    private $sendActivationLinkService;

    /**
     * @var GenerateTokenForUser
     */
    private $authTokenGenerator;

    /**
     * @var Locale
     */
    private $localeService;


    public function __construct(
        Validation $validation,
        SendActivationLink $sendActivationLinkService,
        GenerateAuthToken $authTokenGenerator,
        Locale $localeService
    )
    {
        $this->validation = $validation;
        $this->sendActivationLinkService = $sendActivationLinkService;
        $this->authTokenGenerator = $authTokenGenerator;
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

        $userCredentials = $this->insertUserWithCredentials($email, $plainPassword, $locale, $newUser);
        $userCredentials->replaceAuthenticationToken($this->authTokenGenerator->forUser($userCredentials));
        $this->sendActivationLinkService->call($userCredentials, $locale);

        return $userCredentials->user;
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
            throw new BadRequest("Invalid credentials.", $errors);
        }

        return UserCredentials::register($email, $password, $locale, $user);
    }

}
