<?php namespace Groupeat\Auth\Html;

use Groupeat\Admin\Forms\ResetPasswordForm;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Html\Controller;
use Input;
use Password;

class AuthController extends Controller {

    public function activate($token)
    {
        try
        {
            app('ActivateUserService')->call($token);

            return $this->panelView(
                'auth::activation.panel.title',
                trans('auth::activation.panel.text'),
                'success'
            );
        }
        catch (NotFound $exception)
        {
            return $this->panelView(
                'auth::activation.panel.errors.title',
                trans('auth::activation.panel.errors.wrongToken'),
                'danger'
            );
        }
    }

    public function showResetPasswordForm($token)
    {
        return $this->panelView(
            'auth::resetPassword.panel.title',
            new ResetPasswordForm,
            'warning'
        );
    }

    public function resetPassword($token)
    {
        try
        {
            app('ResetPasswordService')->call(
                $token,
                Input::get('email'),
                Input::get('password'),
                Input::get('password_confirmation')
            );

            return $this->panelView(
                'auth::resetPassword.panel.title',
                trans('auth::resetPassword.panel.text'),
                'info'
            );
        }
        catch (Exception $exception)
        {
            return $this->redirectBackWithError('auth::resetPassword.'.$exception->getMessage());
        }
    }

}
