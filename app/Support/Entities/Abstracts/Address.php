<?php
namespace Groupeat\Support\Entities\Abstracts;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasPosition;
use Groupeat\Support\Presenters\Address as AddressPresenter;

abstract class Address extends Entity
{
    use HasPosition;

    protected $fillable = ['street', 'details', 'city', 'postcode', 'state', 'country', 'latitude', 'longitude'];

    public function getRules()
    {
        return [
            'street' => 'required',
            'city' => 'required',
            'postcode' => 'required|digits:5',
            'state' => 'required',
            'country' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function getPresenter()
    {
        return new AddressPresenter($this);
    }

    protected function getPostcodeAttribute()
    {
        return (int) $this->attributes['postcode']; // TODO: use casting instead
    }
}
