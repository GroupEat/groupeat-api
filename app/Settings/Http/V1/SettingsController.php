<?php
namespace Groupeat\Settings\Http\V1;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Entities\CustomerSettings;
use Groupeat\Settings\Jobs\UpdateSettings;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class SettingsController extends Controller
{
    public function index(Customer $customer)
    {
        $this->auth->assertSame($customer);

        return $this->itemResponse(CustomerSettings::findByCustomerOrFail($customer));
    }

    public function update(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $settings = $this->dispatch(new UpdateSettings(
            $customer,
            $this->json()->all()
        ));

        return $this->itemResponse($settings);
    }
}
