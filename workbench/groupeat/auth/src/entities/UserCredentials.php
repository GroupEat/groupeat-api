<?php namespace Groupeat\Auth\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Entities\Entity;
use Hash;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserTrait;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UserCredentials extends Entity implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait, SoftDeletingTrait;

    protected $hidden = ['password'];


    public function getRules()
    {
        return [
            'email' => 'email|required',
            'password' => 'min:6|required',
            'user_id' => 'required',
            'user_type' => 'required',
        ];
    }

    public function user()
    {
        return $this->morphTo();
    }

    public function isActivated()
    {
        return !empty($this->activated_at);
    }

    protected function setUserAttribute(User $user)
    {
        return $this->setPolymorphicAttribute('user', $user);
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
