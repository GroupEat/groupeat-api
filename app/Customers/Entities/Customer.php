<?php
namespace Groupeat\Customers\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Entities\Abstracts\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Entity implements User
{
    use HasCredentials, SoftDeletes;

    protected $dates = [self::DELETED_AT];
    protected $fillable = ['firstName', 'lastName', 'phoneNumber'];

    public function getRules()
    {
        return [
            'firstName' => 'min:1',
            'lastName' => 'min:1',
            'phoneNumber' => ['regex:/^0[0-9]([ .-]?[0-9]{2}){4}$/'],
        ];
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $phoneNumber
     *
     * @return static
     */
    public static function addExternalCustomer($firstName, $lastName, $phoneNumber)
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
}
