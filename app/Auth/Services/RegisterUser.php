<?php
namespace Groupeat\Auth\Services;

use Closure;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Services\Locale;
use Illuminate\Events\Dispatcher;
use Illuminate\Validation\Factory as Validation;

class RegisterUser
{
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var Dispatcher
     */
    private $events;

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
        Dispatcher $events,
        GenerateAuthToken $authTokenGenerator,
        Locale $localeService
    ) {
        $this->validation = $validation;
        $this->events = $events;
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
    public function call($email, $plainPassword, $locale, User $userType, Closure $additionalValidationCallback = null)
    {
        $this->localeService->assertAvailable($locale);
        $this->assertValidCredentials($email, $plainPassword, $additionalValidationCallback);

        $userCredentials = UserCredentials::register($email, $plainPassword, $locale, $userType->newInstance());
        $userCredentials->replaceAuthenticationToken($this->authTokenGenerator->forUser($userCredentials));

        $this->events->fire('userHasRegistered', [$userCredentials]);

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
