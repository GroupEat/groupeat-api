<?php namespace Groupeat\Customers\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Support\Entities\Entity;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Customer extends Entity implements User {

    use HasCredentials, SoftDeletingTrait;


    public function getRules()
    {
        return [];
    }

    public function addresses()
    {
        return $this->hasMany('Groupeat\Customers\Entities\Address');
    }

}
