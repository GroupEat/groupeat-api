<?php
namespace Groupeat\Customers\Presenters;

use Groupeat\Auth\Presenters\Traits\HasCredentials;
use Groupeat\Support\Presenters\Presenter;

class CustomerPresenter extends Presenter
{
    use HasCredentials;

    public function presentFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function presentFullNameWithPhoneNumber()
    {
        return $this->presentFullName().' ('.$this->phoneNumber.')';
    }
}
