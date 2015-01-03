<?php namespace Groupeat\Auth\Entities\Traits;

use App;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;

trait HasCredentials {

    /**
     * @param string $email
     *
     * @return User|null
     */
    public static function findByEmail($email)
    {
        return UserCredentials::where('email', $email)->first();
    }

    public function getEmailAttribute()
    {
        return $this->credentials->email;
    }

    public function isActivated()
    {
        return $this->credentials->isActivated();
    }

    public function credentials()
    {
        return $this->morphOne('Groupeat\Auth\Entities\UserCredentials', 'user');
    }

}
