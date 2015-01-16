<?php namespace Groupeat\Auth\Html;

use App;
use Groupeat\Admin\Forms\ResetPasswordForm;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Html\Controller;
use Input;
use Lang;
use Password;

class AuthController extends Controller {

    public function activate($token)
    {
        try
        {
            App::make('ActivateUserService')->call($token);

            return panelView(
                'auth::activation.panel.title',
                Lang::get('auth::activation.panel.text'),
                'success'
            );
        }
        catch (NotFound $exception)
        {
            return panelView(
                'auth::activation.panel.errors.title',
                Lang::get('auth::activation.panel.errors.wrongToken'),
                'danger'
            );
        }
    }

    public function showResetPasswordForm($token)
    {
        return panelView(
            'auth::resetPassword.panel.title',
            new ResetPasswordForm($token),
            'warning'
        );
    }

    public function resetPassword($token)
    {
        try
        {
            App::make('ResetPasswordService')->call(
                $token,
                Input::get('email'),
                Input::get('password'),
                Input::get('password_confirmation')
            );

            return panelView(
                'auth::resetPassword.panel.title',
                Lang::get('auth::resetPassword.panel.text'),
                'info'
            );
        }
        catch (Forbidden $exception)
        {
            return $this->redirectBackWithError('auth::resetPassword.'.$exception->getMessage());
        }
    }

}
