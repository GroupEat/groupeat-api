<?php
namespace Groupeat\Settings\Http\V1;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Commands\UpdateSettings;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class SettingsController extends Controller
{
    public function index(Customer $customer)
    {
        $this->auth->assertSame($customer);
    }

    public function update(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $this->dispatch(new UpdateSettings(
            $customer,
            $this->json('values')
        ));
    }
}
