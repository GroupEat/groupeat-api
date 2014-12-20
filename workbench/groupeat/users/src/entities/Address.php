<?php namespace Groupeat\Users\Entities;

use Groupeat\Support\Entities\Entity;

class Address extends Entity {

    public $timestamps = false;

    public function getRules()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo('Groupeat\Users\Entities\User');
    }

}
