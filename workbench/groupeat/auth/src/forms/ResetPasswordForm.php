<?php namespace Groupeat\Admin\Forms;

use Former;
use Groupeat\Support\Forms\Form;

class ResetPasswordForm extends Form {

    protected $rules = [
        'password' => 'min:6|required',
        'password_confirmation' => 'same:password',
    ];

    /**
     * @var string
     */
    protected $token;


    public function __construct($token)
    {
        parent::__construct();

        $this->token = $token;
    }

    protected function add($content)
    {
        return $content
            . $this->text('email')
            . $this->password()
            . $this->password('password_confirmation')
            . $this->submit('submit', 'warning');
    }

}
