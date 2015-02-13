<?php namespace Groupeat\Restaurants\Presenters;

use Groupeat\Support\Presenters\Presenter;

class ProductFormat extends Presenter {

    public function presentPrice()
    {
        return $this->formatPrice($this->object->price);
    }

}
