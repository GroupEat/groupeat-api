<?php namespace Groupeat\Admin\Forms;

use Groupeat\Support\Forms\Form;

class ResetPasswordForm extends Form {

    protected $rules = [
        'password' => 'min:6|required',
        'password_confirmation' => 'same:password',
    ];


    protected function add($content)
    {
        return $content
            . $this->text('email')
            . $this->password()
            . $this->password('password_confirmation')
            . $this->submit('submit', 'warning');
    }

}
