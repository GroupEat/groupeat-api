<?php
namespace Groupeat\Support\Entities\Traits;

use Groupeat\Support\Values\PhoneNumber;

trait HasPhoneNumber
{
    protected function getPhoneNumberAttribute()
    {
        if (!empty($this->attributes['phoneNumber'])) {
            return new PhoneNumber($this->attributes['phoneNumber']);
        }
    }

    protected function setPhoneNumberAttribute(PhoneNumber $phoneNumber = null)
    {
        if (!is_null($phoneNumber)) {
            $this->attributes['phoneNumber'] = $phoneNumber->value();
        }
    }
}
