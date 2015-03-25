<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;

class FoodType extends Entity
{
    public $timestamps = false;

    public function getRules()
    {
        return [
            'label' => 'required|string',
        ];
    }

    /**
     * @param string $label
     *
     * @return FoodType or null if not found
     */
    public static function findByLabel($label)
    {
        return static::where('label', $label)->first();
    }
}
