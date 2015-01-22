<?php namespace Groupeat\Restaurants\Entities\Abstracts;

use Groupeat\Support\Entities\Entity;

abstract class Window extends Entity {

    public $timestamps = false;


    public function getRules()
    {
        return [
            'restaurant_id' => 'required|integer',
            'starting_at' => 'required',
            'ending_at' => 'required',
        ];
    }

}
