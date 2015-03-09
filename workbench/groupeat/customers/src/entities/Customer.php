<?php
namespace Groupeat\Customers\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Support\Entities\Entity;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Customer extends Entity implements User
{
    use HasCredentials, SoftDeletingTrait;

    protected $fillable = ['firstName', 'lastName', 'phoneNumber'];

    public function getRules()
    {
        return [
            'firstName' => 'min:1',
            'lastName' => 'min:1',
            'phoneNumber' => ['regex:/^0[0-9]([ .-]?[0-9]{2}){4}$/'],
        ];
    }

    public function address()
    {
        return $this->hasOne('Groupeat\Customers\Entities\Address');
    }
}
