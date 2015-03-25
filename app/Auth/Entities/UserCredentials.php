<?php
namespace Groupeat\Auth\Entities;

use Carbon\Carbon;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Entities\Abstracts\Entity;
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

    protected $dates = ['activated_at'];
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

        // The user should be save in order to have its id
        $user->save();
        $userCredentials->user = $user;
        $userCredentials->save();

        return $userCredentials;
    }

    public function getBy($key, $value)
    {
        throw new \BadMethodCallException("Not implemented");
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
     */
    public function activate(Carbon $now = null)
    {
        $now = $now ?: $this->freshTimestamp();

        $this->activationToken = null;
        $this->activated_at = $now;
        $this->save();
    }

    /**
     * @param string $token
     */
    public function replaceAuthenticationToken($token)
    {
        $this->token = $token;
        $this->save();
    }

    public function discardPasswordAndToken()
    {
        $this->hashAndSetPassword("WAITING FOR PASSWORD RESET");
        $this->discardToken();
    }

    /**
     * @param $password
     * @param $token
     */
    public function resetPassword($password, $token)
    {
        $this->hashAndSetPassword($password);
        $this->replaceAuthenticationToken($token);
    }

    public function discardToken()
    {
        $this->token = null;
        $this->save();
    }

    /**
     * @param string $password
     */
    public function hashAndSetPassword($password)
    {
        $this->attributes['password'] = Hash::make($password);
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
        // Not implemented
    }

    public function setRememberToken($value)
    {
        // Not implemented
    }

    public function getRememberTokenName()
    {
        // Not implemented
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
        if (!$this->exists) {
            $this->hashAndSetPassword($password);
        }
    }
}
