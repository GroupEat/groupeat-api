<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Support\Entities\Entity;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Restaurant extends Entity implements User {

    use HasCredentials, SoftDeletingTrait;

    protected $fillable = ['name', 'phoneNumber'];


    public function getRules()
    {
        return [
            'name' => 'required',
            'phoneNumber' => ['regex:/^0[0-9]([ .-]?[0-9]{2}){4}$/'],
        ];
    }

    public function foodTypes()
    {
        return $this->belongsToMany('Groupeat\Restaurants\Entities\FoodType');
    }

    public function address()
    {
        return $this->hasOne('Groupeat\Restaurants\Entities\Address');
    }

}
