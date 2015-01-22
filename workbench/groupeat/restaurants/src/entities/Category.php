<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Entity;

class Category extends Entity {

    public $timestamps = false;


    /**
     * @param string $label
     *
     * @return Category|null
     */
    public static function findByLabel($label)
    {
        return static::where('label', $label)->first();
    }

    public function getRules()
    {
        return [
            'label' => 'required',
        ];
    }

}
