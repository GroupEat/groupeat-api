<?php namespace Groupeat\Customers\Api\V1;

use App;
use Auth;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Api\V1\Controller;
use Input;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends Controller {

    public function index()
    {
        return Customer::all();
    }

    public function showCurrentUser()
    {
        return Auth::customer();
    }

    public function store()
    {
        $customer = App::make('RegisterUserService')
            ->call(Input::get('email'), Input::get('password'), new Customer);

        $id = $customer->id;
        $token = App::make('GenerateTokenForUserService')->call(Input::get('email'), Input::get('password'));

        return $this->response->array(compact('id', 'token'))->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroyCurrentUser()
    {
        App::make('DeleteUserService')->call(Auth::customer());
    }

}
