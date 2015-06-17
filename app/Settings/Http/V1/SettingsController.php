<?php
namespace Groupeat\Settings\Http\V1;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Jobs\UpdateSettings;
use Groupeat\Settings\Support\SettingBag;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class SettingsController extends Controller
{
    public function index(Customer $customer)
    {
        $this->auth->assertSame($customer);

        return $this->itemResponse(new SettingBag($customer));
    }

    public function update(Customer $customer)
    {
        $this->auth->assertSame($customer);

        $settingBag = $this->dispatch(new UpdateSettings(
            $customer,
            $this->json()->all()
        ));

        return $this->itemResponse($settingBag);
    }
}
