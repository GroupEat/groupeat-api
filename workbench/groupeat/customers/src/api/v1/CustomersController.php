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
        $registerUser = App::make('RegisterUser');

        if (!$registerUser->call(Input::get('email'), Input::get('password'), new Customer))
        {
            throw new StoreResourceFailedException(
                Lang::get('customers::errors.registration_failed'),
                $registerUser->errors()
            );
        }

        return $this->response->created();
    }

    public function destroy(Customer $customer)
    {
        $deleteUser = App::make('DeleteUser');

        if (!$deleteUser->call($customer))
        {
            throw new DeleteResourceFailedException(Lang::get('customers::errors.deletion_failed'));
        }
    }

}
