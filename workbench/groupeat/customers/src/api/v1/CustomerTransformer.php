<?php namespace Groupeat\Customers\Api\V1;

use Groupeat\Customers\Entities\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    public function transform(Customer $customer)
    {
        return [
            'id' => (int) $customer->id,
            'email' => $customer->credentials->email,
            'firstName' => $customer->firstName,
            'lastName' => $customer->lastName,
            'phoneNumber' => $customer->phoneNumber,
            'locale' => $customer->credentials->locale,
            'activated' => $customer->isActivated(),
        ];
    }

}
