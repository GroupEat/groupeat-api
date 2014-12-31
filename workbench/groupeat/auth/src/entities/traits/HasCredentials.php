<?php namespace Groupeat\Auth\Entities\Traits;

use App;
// use Groupeat\Auth\Entities\Scope\SoftDeletingByCredentialsScope;

trait HasCredentials {

    /**
     * We apply a global scope to all the users so that when we list them
     * only those that have existing credentials appears.
     * 'existing' means that the deleted_at field's value is null
     */
    public static function bootCredentials()
    {
        // static::addGlobalScope(new SoftDeletingByCredentialsScope);
    }

    /**
     * @return string Email retrieved through the credentials relationship
     */
    public function getEmailAttribute()
    {
        return $this->credentials->email;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne The relationship giving access to the credentials
     */
    public function credentials()
    {
        return $this->morphOne('Groupeat\Auth\Entities\UserCredentials', 'user');
    }

}
