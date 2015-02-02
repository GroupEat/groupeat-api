<?php namespace Groupeat\Support\Html;

use Groupeat\Support\Forms\Form;
use Illuminate\Routing\Controller as IlluminateController;
use Input;
use Redirect;
use Validator;

abstract class Controller extends IlluminateController {

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

    protected function redirectBackWithError($langKey)
    {
        $error = trans($langKey);

        return Redirect::back()->withErrors(compact('error'))->withInput();
    }

}
