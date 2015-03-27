<?php
namespace Groupeat\Admin\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Support\Entities\Abstracts\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Entity implements User
{
    use HasCredentials, SoftDeletes;

    protected $dates = [self::DELETED_AT];

    public function getRules()
    {
        return [
            'firstName' => 'required',
            'lastName' => 'required',
        ];
    }
}
