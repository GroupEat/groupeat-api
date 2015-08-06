<?php
namespace Groupeat\Restaurants\Presenters;

use Groupeat\Support\Presenters\Presenter;

class ProductFormatPresenter extends Presenter
{
    public function presentPrice()
    {
        return $this->formatPrice($this->object->price);
    }
}
