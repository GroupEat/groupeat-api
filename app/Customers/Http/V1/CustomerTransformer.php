<?php
namespace Groupeat\Customers\Http\V1;

use Groupeat\Customers\Entities\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    public function transform(Customer $customer)
    {
        $data = [
            'id' => $customer->id,
            'isExternal' => $customer->isExternal,
            'firstName' => $customer->firstName,
            'lastName' => $customer->lastName,
            'phoneNumber' => $customer->phoneNumber,
        ];

        if (!$customer->isExternal) {
            $data['email'] = $customer->credentials->email;
            $data['locale'] = $customer->credentials->locale;
            $data['activated'] = $customer->isActivated();
        }

        return $data;
    }
}
