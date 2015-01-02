<?php namespace Groupeat\Auth;

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

        if ($userCredentials->token != $this->token)
        {
            throw new Unauthorized("Obsolete token.");
        }

        return $this->setUserCredentials($userCredentials);
    }

    /**
     * @return bool
     */
    public function check()
    {
        return !empty($this->userCredentials);
    }

    /**
     * @return bool
     */
    public function checkOrFail()
    {
        if (!$this->check())
        {
            throw new Unauthorized("No authenticated user.");
        }

        return true;
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
     * @param UserCredentials $userCredentials
     *
     * @return UserCredentials
     */
    public function setUserCredentials(UserCredentials $userCredentials)
    {
        if (! $userCredentials->user instanceof User)
        {
            throw new Unauthorized("The user corresponding to these credentials does not exist.");
        }

        $this->auth->setUser($userCredentials);
        $this->userCredentials = $userCredentials;

        return $this->userCredentials;
    }

    /**
     * Check if the authenticated user is of a specific type by giving a full class name.
     *
     * @param string $className
     *
     * @return bool
     */
    public function isOfType($className)
    {
        if (!$this->check())
        {
            return false;
        }

        return $this->userCredentials->user_type == $className;
    }

    /**
     * @param string $className
     *
     * @return User
     */
    public function assertTypeAndGetUser($className)
    {
        $this->checkOrFail();

        $shortType = $this->toShortType($className);

        if (!$this->isOfType($className))
        {
            throw new Unauthorized("Should be authenticated as $shortType.");
        }

        return $this->user();
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        foreach ($this->userTypes as $shortType => $className)
        {
            if ($method == 'is'.ucfirst($shortType))
            {
                return $this->isofType($className);
            }

            if ($method == $shortType)
            {
                return $this->assertTypeAndGetUser($className);
            }
        }

        return parent::__call($method, $parameters);
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function addUserType(User $type)
    {
        $className = get_class($type);
        $shortType = $this->toShortType($className);

        if (empty($this->userTypes[$shortType]))
        {
            $this->userTypes[$shortType] = $className;

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getUserTypes()
    {
        return $this->userTypes;
    }

    /**
     * @param string $className
     *
     * @return string
     */
    private function toShortType($className)
    {
        $parts = explode('\\', $className);

        return strtolower(array_pop($parts));
    }

}
