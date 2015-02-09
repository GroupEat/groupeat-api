<?php namespace Groupeat\Admin\Forms;

use Groupeat\Support\Forms\Form;

class LoginForm extends Form {

    protected $rules = [
        'email' => 'email|required',
        'password' => 'min:6|required',
    ];


    protected function add($content)
    {
        return $content
            . $this->text('email')
            . $this->password()
            . $this->submit('login', 'danger');
    }

}
