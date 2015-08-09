<?php
namespace Groupeat\Customers\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasPhoneNumber;
use Groupeat\Support\Values\PhoneNumber;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Entity implements User
{
    use HasCredentials, HasPhoneNumber, SoftDeletes {
        HasCredentials::isActivated as isActivatedThroughCredentials;
    }

    protected $dates = [self::DELETED_AT];
    protected $fillable = ['firstName', 'lastName', 'phoneNumber'];

    public function getRules()
    {
        return [
            'firstName' => 'min:1',
            'lastName' => 'min:1',
        ];
    }

    /**
     * @param string      $firstName
     * @param string      $lastName
     * @param PhoneNumber $phoneNumber
     *
     * @return static
     */
    public static function addExternalCustomer($firstName, $lastName, PhoneNumber $phoneNumber = null)
    {
        $customer = new static;
        $customer->isExternal = true;
        $customer->firstName = $firstName;
        $customer->lastName = $lastName;
        $customer->phoneNumber = $phoneNumber;
        $customer->save();

        return $customer;
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function isActivated()
    {
        return $this->isExternal ? true : $this->isActivatedThroughCredentials();
    }
}
