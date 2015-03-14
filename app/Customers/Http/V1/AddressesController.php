<?php
namespace Groupeat\Customers\Http\V1;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Entities\PredefinedAddress;
use Groupeat\Customers\Services\ChangeCustomerAddress;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class AddressesController extends Controller
{
    public function show(Customer $customer)
    {
        $this->auth->assertSame($customer);

        if (!$customer->address) {
            return $this->response->errorNotFound("No address has been set for this customer.");
        }

        return $this->itemResponse($customer->address);
    }

    public function change(ChangeCustomerAddress $changeCustomerAddress, Customer $customer)
    {
        $this->auth->assertSame($customer);

        $address = $changeCustomerAddress->call(
            $customer,
            $this->json()->all()
        );

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
