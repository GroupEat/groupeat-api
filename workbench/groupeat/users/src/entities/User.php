<?php namespace Groupeat\Users\Entities;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Support\Facades\Hash;
use Groupeat\Support\Entities\Entity;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Entity implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $hidden = ['password'];


    public function getRules()
    {
        return [
            'password' => 'min:6',
            'email' => 'email|required|unique:'.$this->table,
        ];
    }

    public function addresses()
    {
        return $this->hasMany('Groupeat\Users\Entities\Address');
    }

    protected function setPasswordAttribute($password)
    {
        $this->hashPasswordBeforeInsertion($password);
    }

    private function hashPasswordBeforeInsertion($password)
    {
        if (!$this->exists)
        {
            return $this->attributes['password'] = Hash::make($password);
        }
    }

}
