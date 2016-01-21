<?php
namespace Groupeat\Customers\Http\V1;

use Groupeat\Customers\Jobs\UpdateAddress;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Entities\PredefinedAddress;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class AddressesController extends Controller
{
    public function show(Customer $customer)
    {
        $this->auth->assertSame($customer);

        if (!$customer->address) {
            throw new NotFound(
                'noAddressForThisCustomer',
                "No address has been set for this customer."
            );
        }

        return $this->itemResponse($customer->address);
    }

    public function update(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $attributes = $this->json()->all();
        // TODO: make route consistent: wrap latitude and longitude into location when Ionic Deploy is integrated
        $attributes['location'] = getPointFromLocationArray([
            'latitude' => $attributes['latitude'],
            'longitude' => $attributes['longitude'],
        ]);
        unset($attributes['latitude'], $attributes['longitude']);

        $address = $this->dispatch(new UpdateAddress($customer, $attributes));

        return $this->itemResponse($address);
    }

    public function predefinedIndex()
    {
        return $this->collectionResponse(
            PredefinedAddress::all(),
            new AddressTransformer
        );
    }
}
