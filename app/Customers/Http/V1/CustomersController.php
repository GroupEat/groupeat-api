<?php
namespace Groupeat\Customers\Http\V1;

use Groupeat\Auth\Http\V1\UserTransformer;
use Groupeat\Customers\Jobs\RegisterCustomer;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Groupeat\Support\Values\PhoneNumber;
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

        $data = $this->allJson();

        if (!empty($data['phoneNumber'])) {
            $data['phoneNumber'] = new PhoneNumber($data['phoneNumber']);
        }

        $customer->update($data);

        return $this->itemResponse($customer);
    }

    public function register()
    {
        $customer = $this->dispatch(new RegisterCustomer(
            $this->json('email'),
            $this->json('password'),
            $this->json('locale')
        ));

        return $this->itemResponse($customer, new UserTransformer)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function unregister(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $customer->delete();
    }
}
