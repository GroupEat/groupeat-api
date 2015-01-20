<?php namespace Groupeat\Customers\Api\V1;

use App;
use Auth;
use Groupeat\Customers\Entities\Customer;
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

        $customer->update(Input::all());

        return $this->itemResponse($customer);
    }

    public function register()
    {
        $email = Input::get('email');
        $password = Input::get('password');

        App::make('RegisterCustomerService')->call($email, $password, Input::get('locale'));

        return $this->api->raw()
            ->get('auth/token', compact('email', 'password'))
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

        $address = App::make('ChangeCustomerAddressService')->call($customer, Input::all());

        return $this->itemResponse($address);
    }

}
