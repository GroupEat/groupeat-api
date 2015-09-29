<?php
namespace Groupeat\Restaurants\Entities\Abstracts;

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Entities\Abstracts\Entity;

abstract class Window extends Entity
{
    public $timestamps = false;

    public function getRules()
    {
        return [
            'restaurantId' => 'required',
            'start' => 'required',
            'end' => 'required',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
