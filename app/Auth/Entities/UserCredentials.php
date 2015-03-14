<?php
namespace Groupeat\Auth\Entities;

use Carbon\Carbon;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\Unauthorized;
use Hash;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface;
use Tymon\JWTAuth\Providers\User\UserInterface;

class UserCredentials extends Entity implements Authenticatable, CanResetPasswordInterface, UserInterface
{
    use CanResetPasswordTrait;

    public $timestamps = false;

    protected $hidden = ['password', 'token', 'activationToken'];

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

    public static function boot()
    {
        static::saved(function (UserCredentials $credentials) {
            if ($credentials->user) {
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

        if (!$userCredentials) {
            static::throwNotFoundByEmailException($email);
        }

        return $userCredentials;
    }

    /**
     * @param string $email
     *
     * @return UserCredentials or null if not found
     */
    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * @param string $email
     */
    public static function throwNotFoundByEmailException($email)
    {
        throw new NotFound(
            ['email' => ['notFound' => []]],
            "No user with $email e-mail address found."
        );
    }

    public static function throwBadPasswordException()
    {
        throw new Unauthorized(
            ['password' => ['invalid' => []]],
            "Cannot authenticate with bad password."
        );
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

        dbTransaction(function () use ($userCredentials, $user) {
            // The user should be save in order to have its id
            $user->save();
            $userCredentials->user = $user;
            $userCredentials->save();
        });

        return $userCredentials;
    }

    public function getBy($key, $value)
    {
        dump('UserCredentials '.$key.' '.$value);
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
     * @param string $token
     *
     * @return $this
     */
    public function replaceAuthenticationToken($token)
    {
        $this->token = $token;
        $this->save();

        return $this;
    }

    /**
     * @param string $plainPassword
     * @param string $authenticationToken
     *
     * @return $this
     */
    public function resetPassword($plainPassword, $authenticationToken)
    {
        $this->token = $authenticationToken;
        $this->setPassword($plainPassword);
        $this->save();

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
        if (!$this->exists) {
            $this->setPassword($plainPassword);
        }
    }
}
