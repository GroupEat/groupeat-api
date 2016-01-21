<?php
namespace Groupeat\Customers\Entities;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasPhoneNumber;
use Groupeat\Support\Exceptions\Forbidden;
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

    public static function addExternalCustomer(
        string $firstName,
        string $lastName,
        PhoneNumber $phoneNumber = null
    ): Customer {
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

    public function isActivated(): bool
    {
        return $this->isExternal ? true : $this->isActivatedThroughCredentials();
    }

    public function getMissingAttributes(): array
    {
        return collect(['firstName', 'lastName', 'phoneNumber'])->filter(function ($attribute) {
            return empty($this->$attribute);
        })->all();
    }

    public function assertNoMissingInformation()
    {
        $missingAttributes = $this->getMissingAttributes();

        if (!empty($missingAttributes)) {
            $str = implode(', ', $missingAttributes);

            throw new Forbidden(
                'missingCustomerInformation',
                "The attributes [$str] are missing for customer {$this->toShortString()}"
            );
        }
    }
}
