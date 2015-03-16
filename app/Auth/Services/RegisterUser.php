<?php
namespace Groupeat\Auth\Services;

use Closure;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Services\Locale;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory as Validation;

class RegisterUser
{
    private $validation;
    private $events;
    private $generateAuthToken;
    private $localeService;

    public function __construct(
        Validation $validation,
        Dispatcher $events,
        GenerateAuthToken $generateAuthToken,
        Locale $localeService
    ) {
        $this->validation = $validation;
        $this->events = $events;
        $this->generateAuthToken = $generateAuthToken;
        $this->localeService = $localeService;
    }

    /**
     * @param string   $email
     * @param string   $plainPassword
     * @param string   $locale
     * @param User     $userType
     * @param callable $additionalValidationCallback
     *
     * @return User
     */
    public function call($email, $plainPassword, $locale, User $userType, Closure $additionalValidationCallback = null)
    {
        $this->localeService->assertAvailable($locale);
        $this->assertValidCredentials($email, $plainPassword, $additionalValidationCallback);

        $userCredentials = UserCredentials::register($email, $plainPassword, $locale, $userType->newInstance());
        $userCredentials->replaceAuthenticationToken($this->generateAuthToken->call($userCredentials));

        $this->events->fire(new UserHasRegistered($userCredentials));

        return $userCredentials->user;
    }

    private function assertValidCredentials($email, $password, Closure $additionalCallback = null)
    {
        $credentials = compact('email', 'password');

        $rules = [
            'email' => 'email|required|unique:'.UserCredentials::table(),
            'password' => 'required|min:6',
        ];

        $validator = $this->validation->make($credentials, $rules);

        if (!$validator->passes()) {
            throw new UnprocessableEntity(
                $validator->failed(),
                "Cannot register user with invalid credentials."
            );
        }

        if (!is_null($additionalCallback)) {
            $additionalCallback($credentials);
        }
    }
}
