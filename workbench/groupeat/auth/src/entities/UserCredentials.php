<?php namespace Groupeat\Auth\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Entities\Entity;
use Hash;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;

class UserCredentials extends Entity implements UserInterface, RemindableInterface {

    use RemindableTrait;

    public $timestamps = false;

    protected $hidden = ['password', 'token', 'activationCode'];


    public static function boot()
    {
        static::saved(function(UserCredentials $credentials)
        {
            if ($credentials->user)
            {
                $credentials->user->touch();
            }
        });

        parent::boot();
    }

    public function getRules()
    {
        return [
            'email' => 'email|required',
            'password' => 'min:6|required',
            'user_id' => 'required',
            'user_type' => 'required',
        ];
    }

    public function user()
    {
        return $this->morphTo();
    }

    public function isActivated()
    {
        return !empty($this->activated_at);
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        // Not used
    }

    public function setRememberToken($value)
    {
        // Not used
    }

    public function getRememberTokenName()
    {
        // Not used
    }

    protected function setUserAttribute(User $user)
    {
        return $this->setPolymorphicAttribute('user', $user);
    }

    protected function setPasswordAttribute($password)
    {
        $this->hashPasswordBeforeInsertion($password);
    }

    private function hashPasswordBeforeInsertion($password)
    {
        if (!$this->exists)
        {
            return $this->attributes['password'] = Hash::make($password);
        }
    }

}
