<?php namespace Groupeat\Admin\Html;

use App;
use DB;
use Groupeat\Support\Html\Controller;
use Input;
use Redirect;
use URL;
use View;

class AdminController extends Controller{

    public function login()
    {
        return View::make('admin::login', ['hideNavbar' => true]);
    }

    public function check()
    {
        if (App::make('LoginAdminService')->attempt(Input::get('email'), Input::get('password')))
        {
            return Redirect::intended();
        }

        return Redirect::route('admin.login')->withErrors(['Invalid credentials!']);
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

        if (Input::get('testing'))
        {
            $data['db'] = $data['db'].'-testing';
        }

        $url = URL::to('packages/groupeat/admin/resources/adminer.php').'?'.http_build_query($data);

        return Redirect::to($url);
    }

    public function docs()
    {
        return App::make('GenerateApiDocumentationService')->getHTML(Input::get('generate'));
    }

}
