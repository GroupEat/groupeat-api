<?php namespace Groupeat\Admin\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Support\Entities\Entity;

class Admin extends Entity implements User {

    use HasCredentials;


    public function getRules()
    {
        return [
            'firstName' => 'required',
            'lastName' => 'required',
        ];
    }

}
