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

    const ACTIVATED_AT = 'activatedAt';

    public $timestamps = false;

    protected $dates = [self::ACTIVATED_AT];
    protected $hidden = ['password', 'token'];

    public function getRules()
    {
        return [
            'email' => 'email|required',
            'password' => 'min:6|required',
            'userId' => 'required',
            'userType' => 'required',
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

    public static function findByEmailOrFail(string $email): UserCredentials
    {
        $userCredentials = static::findByEmail($email);

        if (!$userCredentials) {
            static::throwNotFoundByEmailException($email);
        }

        return $userCredentials;
    }

    /**
     * @return UserCredentials|null if not found
     */
    public static function findByEmail(string $email)
    {
        return static::where('email', $email)->first();
    }

    public static function throwNotFoundByEmailException(string $email)
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

    public static function register(string $email, string $password, string $locale, User $user): UserCredentials
    {
        $userCredentials = new static;
        $userCredentials->email = $email;
        $userCredentials->password = $password;
        $userCredentials->locale = $locale;

        // The user should be save in order to have its id
        $user->save();
        $userCredentials->user()->associate($user);
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

    public function isActivated(): bool
    {
        return !empty($this->activatedAt);
    }

    public function activate(Carbon $date = null)
    {
        $date = $date ?: $this->freshTimestamp();

        $this->activatedAt = $date;
        $this->save();
    }

    public function replaceAuthenticationToken(string $token)
    {
        $this->token = $token;
        $this->save();
    }

    public function resetPassword(string $password, string $token)
    {
        $this->hashAndSetPassword($password);
        $this->replaceAuthenticationToken($token);
    }

    public function hashAndSetPassword(string $password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function getAuthIdentifierName()
    {
        return 'id';
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
