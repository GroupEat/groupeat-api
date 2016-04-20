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
    private $generateToken;
    private $localeService;

    public function __construct(
        Validation $validation,
        Dispatcher $events,
        GenerateToken $generateToken,
        Locale $localeService
    ) {
        $this->validation = $validation;
        $this->events = $events;
        $this->generateToken = $generateToken;
        $this->localeService = $localeService;
    }

    public function call(string $email, string $password, string $locale, User $userType): User
    {
        $this->localeService->assertAvailable($locale);
        $this->assertValidCredentials($email, $password);

        $userCredentials = UserCredentials::register($email, $password, $locale, $userType->newInstance());
        $userCredentials->replaceAuthenticationToken($this->generateToken->call($userCredentials));

        $this->events->fire(new UserHasRegistered($userCredentials));

        return $userCredentials->user;
    }

    private function assertValidCredentials(string $email, string $password)
    {
        $credentials = compact('email', 'password');

        $rules = [
            'email' => 'email|required|unique:'.(new UserCredentials)->getTable(),
            'password' => 'required|min:6',
        ];

        $validator = $this->validation->make($credentials, $rules);

        if (!$validator->passes()) {
            throw new UnprocessableEntity(
                $validator->failed(),
                "Cannot register user with invalid credentials."
            );
        }
    }
}
