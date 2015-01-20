<?php namespace Groupeat\Auth\Entities;

use Carbon\Carbon;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Entities\Entity;
use Hash;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;

class UserCredentials extends Entity implements UserInterface, RemindableInterface {

    use RemindableTrait;

    public $timestamps = false;

    protected $hidden = ['password', 'token', 'activationToken'];


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

    /**
     * @param string $email
     *
     * @return UserCredentials
     */
    public static function findByEmailOrFail($email)
    {
        $userCredentials = static::findByEmail($email);

        if (!$userCredentials)
        {
            throw (new ModelNotFoundException)->setModel(get_called_class());
        }

        return $userCredentials;
    }

    /**
     * @param string $email
     *
     * @return UserCredentials|null
     */
    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $locale
     * @param User   $user
     *
     * @return static
     */
    public static function register($email, $password, $locale, User $user)
    {
        $userCredentials = new static;
        $userCredentials->email = $email;
        $userCredentials->password = $password;
        $userCredentials->locale = $locale;
        $userCredentials->user = $user;

        return $userCredentials;
    }

    public function getRules()
    {
        return [
            'email' => 'email|required',
            'password' => 'min:6|required',
            'user_id' => 'required',
            'user_type' => 'required',
            'locale' => 'max:6:required',
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

    /**
     * @param Carbon $now
     *
     * @return $this
     */
    public function activate(Carbon $now = null)
    {
        $now = $now ?: Carbon::now();

        $this->activationToken = null;
        $this->activated_at = $now;

        return $this;
    }

    /**
     * @param string $plainPassword
     *
     * @return $this
     */
    public function setPassword($plainPassword)
    {
        $this->attributes['password'] = Hash::make($plainPassword);

        return $this;
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

    private function hashPasswordBeforeInsertion($plainPassword)
    {
        if (!$this->exists)
        {
            $this->setPassword($plainPassword);
        }
    }

}
