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

        return $customer;
    }

    public function register()
    {
        $email = Input::get('email');
        $password = Input::get('password');

        $customer = App::make('RegisterCustomerService')->call($email, $password, new Customer);

        return $this->api->raw()
            ->put('auth/token', compact('email', 'password'))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function unregister(Customer $customer)
    {
        App::make('DeleteUserService')->call($customer);
    }

}
