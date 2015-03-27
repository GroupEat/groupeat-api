<?php
namespace Groupeat\Restaurants\Entities\Abstracts;

use Groupeat\Support\Entities\Abstracts\Entity;

abstract class Window extends Entity
{
    public $timestamps = false;

    public function getRules()
    {
        return [
            'restaurantId' => 'required',
            'from' => 'required',
            'to' => 'required',
        ];
    }
}
