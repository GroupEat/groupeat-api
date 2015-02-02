<?php namespace Groupeat\Restaurants\Presenters;

use Groupeat\Support\Presenters\Presenter;

class FoodType extends Presenter {

    /**
     * @var array
     */
    private $translations;


    public function __construct($object)
    {
        parent::__construct($object);

        $this->translations = trans('restaurants::foodTypes');
    }

    public function __toString()
    {
        return $this->presentLabel();
    }

    public function presentLabel()
    {
        return (string) $this->translations[$this->object->label];
    }

}
