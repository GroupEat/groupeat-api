<?php namespace Groupeat\Auth;

use App;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\Unauthorized;
use Tymon\JWTAuth\JWTAuth;

class Auth extends JWTAuth {

    /**
     * @var UserCredentials
     */
    private $userCredentials;

    /**
     * @var array
     */
    private $userTypes = [];


    /**
     * @param string|bool $token
     *
     * @return UserCredentials
     */
    public function login($token = false)
    {
        $this->requireToken($token);

        $id = $this->provider->getSubject($this->token);

        $userCredentials = $this->auth->getProvider()->retrieveById($id);

        if (! $userCredentials instanceof UserCredentials)
        {
            throw new Unauthorized("The user corresponding to the authentication token does not exist.");
        }

        $this->assertCorrespondingUserExists($userCredentials);

        if ($userCredentials->token != $this->token)
        {
            throw new Unauthorized("Obsolete token.");
        }

        return $this->forceSetUserCredentials($userCredentials);
    }

    public function logout()
    {
        $this->token = null;
        $this->userCredentials = null;
        App::make('api.auth')->setUser(null);
        $this->auth->logout();
    }

    /**
     * @param $email
     * @param $password
     *
     * @return bool
     */
    public function attemptByCredentials($email, $password)
    {
        if ($this->auth->once(compact('email', 'password')))
        {
            $user = $this->auth->user();

            $this->setUserCredentials($user);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function check()
    {
        return !empty($this->userCredentials);
    }

    public function checkOrFail()
    {
        if (!$this->check())
        {
            throw new Unauthorized("No authenticated user.");
        }
    }

    /**
     * @return UserCredentials
     */
    public function credentials()
    {
        $this->checkOrFail();

        return $this->userCredentials;
    }

    /**
     * @return User
     */
    public function user()
    {
        $this->checkOrFail();

        return $this->userCredentials->user;
    }

    /**
     * @param User $user
     */
    public function assertSame(User $user)
    {
        $this->assertSameType($user);

        if ($this->user()->id != $user->id)
        {
            $type = $this->currentType();
            $givenId = $user->id;
            $currentId = $this->user()->id;

            throw new Unauthorized("Should be authenticated as {$this->toShortType($type)} $givenId instead of $currentId.");
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isSame(User $user)
    {
        if (!$this->check())
        {
            return false;
        }

        return $this->currentType() == $this->typeOf($user) && $this->user()->id == $user->id;
    }

    /**
     * @param User $user
     */
    public function assertSameType(User $user)
    {
        $currentType = $this->currentType();
        $givenType = $this->typeOf($user);

        if ($currentType != $givenType)
        {
            throw new Unauthorized("Should be authenticated as {$this->toShortType($givenType)} instead of {$this->toShortType($currentType)}.");
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isSameType(User $user)
    {
        if (!$this->check())
        {
            return false;
        }

        return $this->currentType() == $this->typeOf($user);
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function addUserType(User $user)
    {
        $userType = $this->typeOf($user);
        $shortType = $this->toShortType($userType);

        if (empty($this->userTypes[$shortType]))
        {
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

    public function shortTypeOf(User $user)
    {
        $type = $this->typeOf($user);
        $shortType = array_search($type, $this->userTypes);

        if ($shortType === false)
        {
            throw new Exception("Type $type has not been added to the available user types.");
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
     * @param UserCredentials $userCredentials
     *
     * @return UserCredentials
     */
    public function setUserCredentials(UserCredentials $userCredentials)
    {
        $this->assertCorrespondingUserExists($userCredentials);

        return $this->forceSetUserCredentials($userCredentials);
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        foreach ($this->userTypes as $shortType => $userType)
        {
            $user = new $userType;

            if ($method == 'is'.ucfirst($shortType))
            {
                return $this->isofType($user);
            }

            if ($method == $shortType)
            {
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

        if ($usesSoftDelete)
        {
            $userQuery->withTrashed();
        }

        $user = $userQuery->getResults();

        if (! $user instanceof User)
        {
            throw new Unauthorized("The user corresponding to these credentials does not exist.");
        }

        if ($usesSoftDelete && $user->trashed())
        {
            throw new Unauthorized("The user corresponding to these credentials has been deleted.");
        }
    }

    /**
     * @param UserCredentials $userCredentials
     *
     * @return UserCredentials
     */
    private function forceSetUserCredentials(UserCredentials $userCredentials)
    {
        $this->auth->setUser($userCredentials);
        $this->userCredentials = $userCredentials;
        $this->auth->getDispatcher()->fire('groupeat.auth.login', [$userCredentials]);

        return $this->userCredentials;
    }

    /**
     * @param string $userType
     *
     * @return string
     */
    private function toShortType($userType)
    {
        return strtolower(removeNamespaceFromClassName($userType));
    }

}
