<?php
namespace Groupeat\Customers\Http\V1;

use Groupeat\Auth\Http\V1\TokenTransformer;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Services\RegisterCustomer;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends Controller
{
    public function show(Customer $customer)
    {
        $this->auth->assertSame($customer);

        return $this->itemResponse($customer);
    }

    public function update(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $customer->update($this->json()->all());

        return $this->itemResponse($customer);
    }

    public function register(RegisterCustomer $registerCustomer)
    {
        $email = $this->json('email');
        $password = $this->json('password');

        $customer = $registerCustomer->call($email, $password, $this->json('locale'));

        $this->statusCode = Response::HTTP_CREATED;

        return $this->itemResponse($customer, new TokenTransformer);
    }

    public function unregister(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $customer->delete();
    }
}
