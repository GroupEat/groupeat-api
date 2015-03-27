<?php
namespace Groupeat\Settings\Handlers\Commands;

use Groupeat\Settings\Commands\UpdateSettings;
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

    public function handle(UpdateSettings $command)
    {
        $customer = $command->getCustomer();

        foreach ($command->getValues() as $label => $value) {
            CustomerSetting::set($customer, $label, $value);
        }

        $this->events->fire(new CustomerHasUpdatedItsSettings(
            $command->getCustomer($customer),
            new SettingBag($customer)
        ));
    }
}
