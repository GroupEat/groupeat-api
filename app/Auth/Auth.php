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
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class Auth implements Provider
{
    /**
     * @var array
     */
    private $userTypes = [];

    private $jwtAuth;
    private $illuminateAuth;
    private $dingoAuth;

    public function __construct(JWTAuth $jwtAuth, Guard $illuminateAuth, DingoAuth $dingoAuth)
    {
        $this->jwtAuth = $jwtAuth;
        $this->illuminateAuth = $illuminateAuth;
        $this->dingoAuth = $dingoAuth;
    }

    // Read the request authorization header and return the authenticated user instance if successful.
    public function authenticate(Request $request, Route $route): UserCredentials
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

    public function login(string $token): UserCredentials
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

        if ($userCredentials->token != $token) {
            throw new Forbidden(
                'obsoleteAuthenticationToken',
                "Obsolete authentication token."
            );
        }

        return $this->credentials();
    }

    public function byCredentials(string $email, string $password)
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
        $this->dingoAuth->setUser(null);
    }

    public function check(): bool
    {
        return !empty($this->illuminateAuth->user());
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

    public function credentials(): UserCredentials
    {
        $this->checkOrFail();

        return $this->illuminateAuth->getUser();
    }

    public function user(): User
    {
        return $this->credentials()->user;
    }

    public function userId(): string
    {
        return $this->user()->id;
    }

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

    public function isSame(User $user): bool
    {
        if (!$this->check()) {
            return false;
        }

        return $this->currentType() == $this->typeOf($user) && $this->userId() == $user->id;
    }

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

    public function isSameType(User $user): bool
    {
        if (!$this->check()) {
            return false;
        }

        return $this->currentType() == $this->typeOf($user);
    }

    public function addUserType(User $user): bool
    {
        $userType = $this->typeOf($user);
        $shortType = $this->toShortType($userType);

        if (empty($this->userTypes[$shortType])) {
            $this->userTypes[$shortType] = $userType;

            return true;
        }

        return false;
    }

    public function currentType(): string
    {
        return $this->typeOf($this->user());
    }

    public function shortTypeOf(User $user): string
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

    public function typeOf(User $user): string
    {
        return get_class($user);
    }

    public function getUserTypes(): array
    {
        return $this->userTypes;
    }

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

    private function toShortType(string $userType): string
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
