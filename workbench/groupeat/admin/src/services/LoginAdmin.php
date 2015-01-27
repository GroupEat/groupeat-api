<?php namespace Groupeat\Admin\Services;

use Groupeat\Auth\Auth;
use Groupeat\Admin\Entities\Admin;
use Groupeat\Support\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginAdmin {

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var bool
     */
    private $isLocal;


    public function __construct(Auth $auth, SessionInterface $session, $isLocal)
    {
        $this->auth = $auth;
        $this->session = $session;
        $this->isLocal = $isLocal;
    }

    /**
     * @return bool
     */
    public function check()
    {
        if ($this->tryToLogBySession())
        {
            return true;
        }

        if ($this->isLocal)
        {
            $admin = Admin::first();

            if ($admin)
            {
                $this->login($admin);

                return true;
            }
        }

        return false;
    }

    /**
     * @param $email
     * @param $password
     *
     * @return bool
     */
    public function attempt($email, $password)
    {
        try
        {
            $this->auth->byCredentials($email, $password);

            $this->login($this->auth->admin());

            return true;
        }
        catch (Exception $exception)
        {
            return false;
        }
    }

    public function logout()
    {
        $this->session->flush($this->getSessionKey());
    }

    /**
     * @return bool
     */
    private function tryToLogBySession()
    {
        $admin = $this->retrieveFromSession();

        if ($admin)
        {
            $this->login($admin);

            return true;
        }

        return false;
    }

    /**
     * @param Admin $admin
     */
    private function login(Admin $admin)
    {
        $this->putInSession($admin);
        $this->auth->setUserCredentials($admin->credentials);
    }

    /**
     * @return bool|Admin
     */
    private function retrieveFromSession()
    {
        $id = $this->session->get($this->getSessionKey());

        if ($id)
        {
            $admin = Admin::find($id);

            if ($admin)
            {
                return $admin;
            }
        }

        return false;
    }

    /**
     * @param Admin $admin
     */
    private function putInSession(Admin $admin)
    {
        $this->session->put($this->getSessionKey(), $admin->id);
    }

    /**
     * @return string
     */
    private function getSessionKey()
    {
        return 'admin_id';
    }

}
