<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Entity;

class FoodType extends Entity {

    public $timestamps = false;


    /**
     * @param string $name
     *
     * @return FoodType|null
     */
    public static function findByType($name)
    {
        return static::where('name', $name)->first();
    }

    public function getRules()
    {
        return [
            'name' => 'required',
        ];
    }

}
