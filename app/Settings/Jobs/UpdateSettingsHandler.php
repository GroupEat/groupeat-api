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

            CustomerSetting::set($customer, $label, $value);
        foreach ($job->getValues() as $label => $value) {
        }

        $this->events->fire(new CustomerHasUpdatedItsSettings(
            new SettingBag($customer)
            $job->getCustomer($customer),
        ));
    }
}
