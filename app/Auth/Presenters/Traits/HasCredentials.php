<?php
namespace Groupeat\Auth\Presenters\Traits;

trait HasCredentials
{
    public function presentMailTo()
    {
        return '<a href="mailto:'.$this->email.'">'.$this->email.'</a>';
    }
}
