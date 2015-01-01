<?php namespace Groupeat\Customers\Api\V1;

use App;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Api\V1\Controller;
use Input;
use Lang;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends Controller {

    public function index()
    {
        return Customer::all();
    }

    public function store()
    {
        $registrationService = App::make('RegisterUserService');
        $token = $registrationService->call(Input::get('email'), Input::get('password'), new Customer);

        return $this->response->array(compact('token'))->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Customer $customer)
    {
        App::make('DeleteUserService')->call($customer);
    }

}
