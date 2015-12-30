<?php
namespace Groupeat\Auth\Entities\Traits;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Forbidden;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasCredentials
{
    public static function findByEmailOrFail(string $email): User
    {
        $user = static::findByEmail($email);

        if (!$user) {
            UserCredentials::throwNotFoundByEmailException($email);
        }

        return $user;
    }

    /**
     * @return User|null if not found
     */
    public static function findByEmail(string $email)
    {
        $credentials = UserCredentials::findByEmail($email);

        if (!$credentials) {
            return;
        }

        if (!$credentials->user) {
            UserCredentials::throwNotFoundException();
        }

        return $credentials->user;
    }

    public function getEmailAttribute()
    {
        return $this->credentials->email;
    }

    public function getLocaleAttribute()
    {
        return $this->credentials->locale;
    }

    public function isActivated(): bool
    {
        return $this->credentials->isActivated();
    }

    public function assertActivated(string $exceptionMessage = '')
    {
        if (!$this->isActivated()) {
            if (empty($exceptionMessage)) {
                $exceptionMessage = "The {$this->toShortString} should be activated.";
            }

            throw new Forbidden(
                "userShouldBeActivated",
                $exceptionMessage
            );
        }
    }

    public function credentials(): MorphOne
    {
        return $this->morphOne(UserCredentials::class, 'user');
    }
}
