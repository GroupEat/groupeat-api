<?php
namespace Groupeat\Auth\Entities\Interfaces;

interface User
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne Relationship giving access to the credentials
     */
    public function credentials();
}
