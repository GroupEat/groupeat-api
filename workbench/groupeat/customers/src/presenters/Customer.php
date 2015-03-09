<?php
namespace Groupeat\Customers\Presenters;

use Groupeat\Auth\Presenters\Traits\HasCredentials;
use Groupeat\Support\Presenters\Presenter;

class Customer extends Presenter
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
