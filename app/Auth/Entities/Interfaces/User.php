<?php
namespace Groupeat\Auth\Entities\Interfaces;

interface User
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne Relationship giving access to the credentials
     */
    public function credentials();

    /**
     * Create a new instance of the current user.
     *
     * @param array $attributes
     * @param bool  $exists
     *
     * @return User
     */
    public function newInstance($attributes = [], $exists = false);

    /**
     * @param bool $isActivated
     */
    public function setIsActivated($isActivated);
}
