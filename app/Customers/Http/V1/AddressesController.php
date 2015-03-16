<?php
namespace Groupeat\Customers\Http\V1;

use Groupeat\Customers\Commands\ChangeAddress;
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

    public function change(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $address = $this->dispatch(new ChangeAddress($customer, $this->json()->all()));

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
