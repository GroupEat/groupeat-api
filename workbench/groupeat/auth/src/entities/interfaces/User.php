<?php namespace Groupeat\Auth\Entities\Interfaces;

interface User {

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne The relationship giving access to the credentials
     */
    public function credentials();

}
