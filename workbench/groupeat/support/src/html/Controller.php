<?php namespace Groupeat\Support\Html;

use App;
use Auth;
use Exception;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Forms\Form;
use Illuminate\Routing\Controller as IlluminateController;
use Input;
use Log;
use Redirect;
use Response;
use Validator;

abstract class Controller extends IlluminateController {

    protected function panelView($title, $panelBody, $panelClass = 'primary', $panelId = 'groupeat-panel')
    {
        $title = translateIfNeeded($title);

        if ($panelBody instanceof Form)
        {
            $panelBody = $panelBody->render();
        }

        return Response::view('support::panel', compact('title', 'panelBody', 'panelClass', 'panelId'));
    }

    /**
     * @param Form $form
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function redirectBackIfInvalid(Form $form)
    {
        $rules = $form->getRules();

        if (!empty($rules))
        {
            $validator = Validator::make(Input::all(), $rules);

            if (!$validator->passes())
            {
                return Redirect::back()->withErrors($validator->errors())->withInput();
            }
        }
    }

    protected function redirectBackWithError($langKey, array $langData = [])
    {
        $error = trans($langKey, $langData);

        return Redirect::back()->withErrors(compact('error'))->withInput();
    }

    protected function assertAuthSameFrom(User $user, $token, $assertSameToken = false)
    {
        try
        {
            Auth::login($token, $assertSameToken);
            Auth::assertSame($user);
        }
        catch (Exception $exception)
        {
            Log::error($exception);
            App::abort(403, $exception->getMessage());
        }
    }

}
