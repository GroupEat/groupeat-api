<?php
namespace Groupeat\Support\Entities\Abstracts;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasLocation;
use Groupeat\Support\Presenters\AddressPresenter;

abstract class Address extends Entity
{
    use HasLocation;

    protected $fillable = ['street', 'details', 'city', 'postcode', 'state', 'country', 'location'];

    public function getRules()
    {
        return [
            'street' => 'required',
            'city' => 'required',
            'postcode' => 'required|digits:5',
            'state' => 'required',
            'country' => 'required',
            'location' => 'required',
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
