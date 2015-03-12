<?php
namespace Groupeat\Restaurants\Presenters;

use Groupeat\Support\Presenters\Presenter;

class FoodType extends Presenter
{
    /**
     * @var array
     */
    private static $translations;

    public function __construct($object)
    {
        parent::__construct($object);

        if (is_null(static::$translations)) {
            static::$translations = trans('restaurants::foodTypes');
        }
    }

    public function __toString()
    {
        return $this->presentLabel();
    }

    public function presentLabel()
    {
        return (string) static::$translations[$this->object->label];
    }
}
