<?php
namespace Groupeat\Restaurants\Presenters;

use Groupeat\Auth\Presenters\Traits\HasCredentials;
use Groupeat\Support\Presenters\Presenter;

class RestaurantPresenter extends Presenter
{
    use HasCredentials;

    public function presentNameWithLocation()
    {
        return $this->object->name.' ('.$this->object->address->city.')';
    }
}
