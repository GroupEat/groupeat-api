<?php namespace Groupeat\Auth\Entities\Traits;

use App;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Forbidden;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasCredentials {

    /**
     * @var bool
     */
    protected $isActivated = null;


    /**
     * @param $email
     *
     * @return User
     */
    public static function findByEmailOrFail($email)
    {
        $user = static::findByEmail($email);

        if (!$user)
        {
            UserCredentials::throwNotFoundByEmailException($email);
        }

        return $user;
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public static function findByEmail($email)
    {
        $credentials = UserCredentials::findByEmail($email);

        if (!$credentials)
        {
            return null;
        }

        if (!$credentials->user)
        {
            UserCredentials::throwNotFoundException();
        }

        return $credentials->user;
    }

    public function getEmailAttribute()
    {
        return $this->credentials->email;
    }

    public function isActivated()
    {
        if (!is_null($this->isActivated))
        {
            return $this->isActivated;
        }

        return $this->credentials->isActivated();
    }

    /**
     * @param string $exceptionMessage
     */
    public function assertActivated($exceptionMessage = null)
    {
        if (!$this->isActivated())
        {
            if (empty($exceptionMessage))
            {
                $exceptionMessage = "The {$this->toShortString} should be activated.";
            }

            throw new Forbidden(
                "userShouldBeActivated",
                $exceptionMessage
            );
        }
    }

    public function credentials()
    {
        return $this->morphOne('Groupeat\Auth\Entities\UserCredentials', 'user');
    }

    public function setIsActivated($isActivated)
    {
        $this->isActivated = (bool) $isActivated;
    }

}
