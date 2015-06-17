<?php
namespace Groupeat\Settings\Jobs;

use Groupeat\Settings\Jobs\UpdateSettings;
use Groupeat\Settings\Entities\CustomerSetting;
use Groupeat\Settings\Events\CustomerHasUpdatedItsSettings;
use Groupeat\Settings\Support\SettingBag;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateSettingsHandler
{
    private $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function handle(UpdateSettings $job)
    {
        $customer = $job->getCustomer();

        foreach ($job->getValues() as $label => $value) {
            CustomerSetting::setByLabel($label, $customer, $value);
        }

        $settingBag = new SettingBag($customer);

        $this->events->fire(new CustomerHasUpdatedItsSettings(
            $job->getCustomer($customer),
            $settingBag
        ));

        return $settingBag;
    }
}
