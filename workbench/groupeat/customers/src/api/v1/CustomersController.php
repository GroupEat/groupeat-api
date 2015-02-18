<?php namespace Groupeat\Customers\Api\V1;

use Auth;
use Groupeat\Customers\Entities\Address;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Entities\PredefinedAddress;
use Groupeat\Support\Api\V1\Controller;
use Input;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends Controller {

    public function show(Customer $customer)
    {
        Auth::assertSame($customer);

        return $this->itemResponse($customer);
    }

    public function update(Customer $customer)
    {
        Auth::assertSame($customer);

        $customer->update(Input::json()->all());

        return $this->itemResponse($customer);
    }

    public function register()
    {
        $email = Input::json('email');
        $password = Input::json('password');

        app('RegisterCustomerService')->call($email, $password, Input::json('locale'));

        return $this->api->raw()
            ->put('auth/token', [], json_encode(compact('email', 'password')))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function unregister(Customer $customer)
    {
        Auth::assertSame($customer);

        $customer->delete();
    }

    public function showAddress(Customer $customer)
    {
        Auth::assertSame($customer);

        if (!$customer->address)
        {
            return $this->response->errorNotFound("No address has been set for this customer.");
        }

        return $this->itemResponse($customer->address);
    }

    public function changeAddress(Customer $customer)
    {
        Auth::assertSame($customer);

        $address = app('ChangeCustomerAddressService')->call(
            $customer,
            Input::json()->all()
        );

        return $this->itemResponse($address);
    }

    public function predefinedAddressesIndex()
    {
        return $this->collectionResponse(
            PredefinedAddress::all(),
            new AddressTransformer
        );
    }

}
