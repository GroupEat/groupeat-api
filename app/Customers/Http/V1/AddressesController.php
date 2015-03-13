<?php
namespace Groupeat\Customers\Http\V1;

use Auth;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Entities\PredefinedAddress;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Input;

class AddressesController extends Controller
{
    public function show(Customer $customer)
    {
        Auth::assertSame($customer);

        if (!$customer->address) {
            return $this->response->errorNotFound("No address has been set for this customer.");
        }

        return $this->itemResponse($customer->address);
    }

    public function change(Customer $customer)
    {
        Auth::assertSame($customer);

        $address = app('ChangeCustomerAddressService')->call(
            $customer,
            Input::json()->all()
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
