<?php namespace Groupeat\Admin\Html;

use App;
use DB;
use Groupeat\Admin\Forms\LoginForm;
use Groupeat\Support\Html\Controller;
use Input;
use Redirect;
use URL;
use View;

class AdminController extends Controller {

    public function showLoginForm()
    {
        return panelView('admin::login.panel.title', new LoginForm, 'danger');
    }

    public function loginCheck()
    {
        if ($response = $this->redirectBackIfInvalid(new LoginForm))
        {
            return $response;
        }

        if (App::make('LoginAdminService')->attempt(Input::get('email'), Input::get('password')))
        {
            return Redirect::intended();
        }

        return $this->redirectBackWithError('admin::login.panel.invalidCredentials');
    }

    public function logout()
    {
        App::make('LoginAdminService')->logout();

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

        $url = URL::to('packages/groupeat/admin/adminer.php').'?'.http_build_query($data);

        return Redirect::to($url);
    }

    public function docs()
    {
        $forceRegenerate = App::isLocal() && Input::get('generate');

        return App::make('GenerateApiDocumentationService')->getHTML($forceRegenerate);
    }

}
