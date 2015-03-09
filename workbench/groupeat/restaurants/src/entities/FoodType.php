<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Entity;

class FoodType extends Entity
{
    public $timestamps = false;

    public function getRules()
    {
        return [
            'label' => 'required',
        ];
    }

    /**
     * @param string $label
     *
     * @return Category|null
     */
    public static function findByLabel($label)
    {
        return static::where('label', $label)->first();
    }
}
