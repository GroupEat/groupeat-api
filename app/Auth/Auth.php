<?php
namespace Groupeat\Auth;

use Exception as BaseException;
use Dingo\Api\Auth\Auth as DingoAuth;
use Dingo\Api\Contract\Auth\Provider;
use Dingo\Api\Routing\Route;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\Unauthorized;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class Auth implements Provider
{
    /**
     * @var array
     */
    private $userTypes = [];

    /**
     * @var bool
     */
    private $allowDifferentToken = false;

    private $jwtAuth;
    private $illuminateAuth;
    private $dingoAuth;

    public function __construct(JWTAuth $jwtAuth, Guard $illuminateAuth, DingoAuth $dingoAuth)
    {
        $this->jwtAuth = $jwtAuth;
        $this->illuminateAuth = $illuminateAuth;
        $this->dingoAuth = $dingoAuth;
    }

    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param Request $request
     * @param Route   $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route)
    {
        $authorizationHeader = $request->header('authorization');

        if (!empty($authorizationHeader)) {
            try {
                // Remove the 'Bearer ' part of the header
                list(, $token) = explode(' ', $authorizationHeader);
            } catch (BaseException $e) {
                $this->throwBadTokenSignatureException($e);
            }

            $this->login($token);
        }

        return $this->credentials();
    }

    /**
     * @param string $token
     * @param bool   $assertSameToken
     *
     * @return UserCredentials
     */
    public function login($token, $assertSameToken = true)
    {
        try {
            $userCredentials = $this->jwtAuth->authenticate($token);
        } catch (TokenInvalidException $e) {
            $this->throwBadTokenSignatureException($e);
        }

        if (! $userCredentials instanceof UserCredentials) {
            throw new Unauthorized(
                'noUserForAuthenticationToken',
                "The user corresponding to the authentication token does not exist."
            );
        }

        $this->assertCorrespondingUserExists($userCredentials);

        if (($assertSameToken && !$this->allowDifferentToken) && ($userCredentials->token != $token)) {
            throw new Forbidden(
                'obsoleteAuthenticationToken',
                "Obsolete authentication token."
            );
        }

        return $this->credentials();
    }

    /**
     * @param $email
     * @param $password
     */
    public function byCredentials($email, $password)
    {
        if ($this->illuminateAuth->once(compact('email', 'password'))) {
            $user = $this->illuminateAuth->getUser();

            $this->assertCorrespondingUserExists($user);

            return;
        }

        if (!$this->illuminateAuth->getLastAttempted()) {
            UserCredentials::throwNotFoundByEmailException($email);
        }

        UserCredentials::throwBadPasswordException();
    }

    public function logout()
    {
        $this->illuminateAuth->logout();
        $this->dingoAuth->setUser(null);
    }

    public function allowDifferentToken()
    {
        $this->allowDifferentToken = true;
    }

    public function denyDifferentToken()
    {
        $this->allowDifferentToken = false;
    }

    /**
     * @return bool
     */
    public function check()
    {
        return !empty($this->illuminateAuth->getUser());
    }

    public function checkOrFail()
    {
        if (!$this->check()) {
            throw new Unauthorized(
                'userMustAuthenticate',
                "No authenticated user."
            );
        }
    }

    /**
     * @return UserCredentials
     */
    public function credentials()
    {
        $this->checkOrFail();

        return $this->illuminateAuth->getUser();
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->credentials()->user;
    }

    /**
     * @return string
     */
    public function userId()
    {
        return $this->user()->id;
    }

    /**
     * @param User $user
     */
    public function assertSame(User $user)
    {
        $this->assertSameType($user);

        if ($this->userId() != $user->id) {
            $type = $this->currentType();
            $givenId = $user->id;
            $currentId = $this->userId();

            throw new Forbidden(
                'wrongAuthenticatedUser',
                "Should be authenticated as {$this->toShortType($type)} $givenId instead of $currentId."
            );
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isSame(User $user)
    {
        if (!$this->check()) {
            return false;
        }

        return $this->currentType() == $this->typeOf($user) && $this->userId() == $user->id;
    }

    /**
     * @param User $user
     */
    public function assertSameType(User $user)
    {
        $currentType = $this->currentType();
        $givenType = $this->typeOf($user);

        if ($currentType != $givenType) {
            throw new Forbidden(
                'wrongAuthenticatedUser',
                "Should be authenticated as {$this->toShortType($givenType)} "
                . "instead of {$this->toShortType($currentType)}."
            );
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isSameType(User $user)
    {
        if (!$this->check()) {
            return false;
        }

        return $this->currentType() == $this->typeOf($user);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function addUserType(User $user)
    {
        $userType = $this->typeOf($user);
        $shortType = $this->toShortType($userType);

        if (empty($this->userTypes[$shortType])) {
            $this->userTypes[$shortType] = $userType;

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function currentType()
    {
        return $this->typeOf($this->user());
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function shortTypeOf(User $user)
    {
        $type = $this->typeOf($user);
        $shortType = array_search($type, $this->userTypes);

        if ($shortType === false) {
            throw new Exception(
                'userTypeNotAvailableForAuthentication',
                "Type $type has not been added to the available user types."
            );
        }

        return $shortType;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function typeOf(User $user)
    {
        return get_class($user);
    }

    /**
     * @return array
     */
    public function getUserTypes()
    {
        return $this->userTypes;
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        foreach ($this->userTypes as $shortType => $userType) {
            $user = new $userType;

            if ($method == 'is'.ucfirst($shortType)) {
                return $this->isofType($user);
            }

            if ($method == $shortType) {
                $this->assertSameType($user);

                return $this->user();
            }
        }

        return parent::__call($method, $parameters);
    }

    private function assertCorrespondingUserExists(UserCredentials $credentials)
    {
        $userQuery = $credentials->user();
        $userType = $userQuery->getModel();
        $usesSoftDelete = method_exists($userType, 'withTrashed');

        if ($usesSoftDelete) {
            $userQuery->withTrashed();
        }

        $user = $userQuery->getResults();

        if (! $user instanceof User) {
            throw new Unauthorized(
                'noUserWithSameCredentials',
                "The user corresponding to these credentials does not exist."
            );
        }

        if ($usesSoftDelete && $user->trashed()) {
            throw new Unauthorized(
                'userHasBeenDeleted',
                "The user corresponding to these credentials has been deleted."
            );
        }

        $credentials->user()->associate($user);
    }

    /**
     * @param string $userType
     *
     * @return string
     */
    private function toShortType($userType)
    {
        return strtolower(class_basename($userType));
    }

    private function throwBadTokenSignatureException(BaseException $e = null)
    {
        throw new Unauthorized(
            'invalidAuthenticationTokenSignature',
            "The token signature is invalid and thus cannot be correctly decoded to an existing user.",
            null,
            [],
            $e
        );
    }
}
