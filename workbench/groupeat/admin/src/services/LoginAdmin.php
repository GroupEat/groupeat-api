<?php namespace Groupeat\Admin\Services;

use Groupeat\Auth\Auth;
use Groupeat\Admin\Entities\Admin;
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
        if ($this->auth->attemptByCredentials($email, $password))
        {
            $this->login($this->auth->admin());

            return true;
        }

        return false;
    }

    private function tryToLogBySession()
    {
        $adminId = $this->session->get($this->getSessionKey());

        if ($adminId)
        {
            $admin = Admin::fin($adminId);

            if ($admin)
            {
                $this->login($admin);

                return true;
            }
        }

        return false;
    }

    /**
     * @param Admin $admin
     */
    private function login(Admin $admin)
    {
        $this->session->put($this->getSessionKey(), $admin->id);
        $this->auth->setUserCredentials($admin->credentials);
    }

    /**
     * @return string
     */
    private function getSessionKey()
    {
        return 'admin_id';
    }

}
