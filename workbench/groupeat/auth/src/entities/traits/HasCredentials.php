<?php namespace Groupeat\Auth\Entities\Traits;

use App;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasCredentials {

    /**
     * @param $email
     *
     * @return User
     */
    public static function findByEmailOrFail($email)
    {
        return static::findByEmail($email);
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
            throw (new ModelNotFoundException)->setModel(get_called_class());
        }

        return $credentials->user;
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
