<?php
namespace Groupeat\Customers\Http\V1;

use Auth;
use Groupeat\Auth\Http\V1\TokenTransformer;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Http\V1\Controller;
use Input;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends Controller
{
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

        $customer = app('RegisterCustomerService')->call($email, $password, Input::json('locale'));

        $this->statusCode = Response::HTTP_CREATED;

        return $this->itemResponse($customer, new TokenTransformer);
    }

    public function unregister(Customer $customer)
    {
        Auth::assertSame($customer);

        $customer->delete();
    }
}
