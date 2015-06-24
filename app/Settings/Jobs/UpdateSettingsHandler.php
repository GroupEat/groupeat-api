<?php
namespace Groupeat\Settings\Jobs;

use Groupeat\Settings\Entities\CustomerSettings;
use Groupeat\Settings\Jobs\UpdateSettings;
use Groupeat\Settings\Events\CustomerHasUpdatedItsSettings;
use Groupeat\Support\Exceptions\BadRequest;
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
        $settings = CustomerSettings::findByCustomerOrFail($customer);
        $settings->customer()->associate($customer);

        foreach ($job->getValues() as $label => $value) {
            if (!in_array($label, CustomerSettings::LABELS)) {
                throw new BadRequest(
                    'undefinedCustomerSetting',
                    "The customer setting with label '$label' does not exist."
                );
            }
            $settings->$label = $value;
        }

        $settings->save();

        $this->events->fire(new CustomerHasUpdatedItsSettings($settings));

        return $settings;
    }
}
