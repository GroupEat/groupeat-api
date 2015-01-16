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
            ->post('auth/token', compact('email', 'password'))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function unregister(Customer $customer)
    {
        App::make('DeleteUserService')->call($customer);
    }

}
