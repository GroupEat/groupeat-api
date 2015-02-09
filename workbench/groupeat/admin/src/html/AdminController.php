<?php namespace Groupeat\Admin\Html;

use App;
use DB;
use Groupeat\Admin\Forms\LoginForm;
use Groupeat\Support\Html\Controller;
use Input;
use Redirect;

class AdminController extends Controller {

    public function showLoginForm()
    {
        return $this->panelView('admin::login.panel.title', new LoginForm, 'danger');
    }

    public function loginCheck()
    {
        if ($response = $this->redirectBackIfInvalid(new LoginForm))
        {
            return $response;
        }

        if (app('LoginAdminService')->attempt(Input::get('email'), Input::get('password')))
        {
            return Redirect::intended();
        }

        return $this->redirectBackWithError('admin::login.panel.invalidCredentials');
    }

    public function logout()
    {
        app('LoginAdminService')->logout();

        return Redirect::home();
    }

    public function PHPinfo()
    {
        phpinfo();
    }

    public function db()
    {
        $data = [
            DB::getConfig('driver') => DB::getConfig('host'),
            'db' => DB::getConfig('database'),
            'username' => DB::getConfig('username'),
        ];

        if (App::isLocal())
        {
            $data['passwd'] = DB::getConfig('password');
        }

        $url = url('packages/groupeat/admin/adminer.php').'?'.http_build_query($data);

        return Redirect::to($url);
    }

    public function docs()
    {
        $forceRegenerate = App::isLocal() && Input::get('generate');

        return app('GenerateApiDocumentationService')->getHTML($forceRegenerate);
    }

}
