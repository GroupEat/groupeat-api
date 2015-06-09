<?php
namespace Groupeat\Customers\Http\V1;

use Groupeat\Auth\Http\V1\TokenTransformer;
use Groupeat\Customers\Jobs\RegisterCustomer;
use Groupeat\Customers\Entities\Customer;
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

    public function register()
    {
        $customer = $this->dispatch(new RegisterCustomer(
            $this->json('email'),
            $this->json('password'),
            $this->json('locale')
        ));

        $this->statusCode = Response::HTTP_CREATED;

        return $this->itemResponse($customer, new TokenTransformer);
    }

    public function unregister(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $customer->delete();
    }
}
