<?php
namespace Groupeat\Settings\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Entities\CustomerSettings;
use Groupeat\Settings\Events\CustomerHasUpdatedItsSettings;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateSettings extends Job
{
    private $customer;
    private $values;

    public function __construct(Customer $customer, array $values)
    {
        $this->customer = $customer;
        $this->values = $values;
    }

    public function handle(Dispatcher $events)
    {
        $settings = CustomerSettings::findByCustomerOrFail($this->customer);
        $settings->customer()->associate($this->customer);

        foreach ($this->values as $label => $value) {
            if (!in_array($label, CustomerSettings::LABELS)) {
                throw new BadRequest(
                    'undefinedCustomerSetting',
                    "The customer setting with label '$label' does not exist."
                );
            }
            $settings->$label = $value;
        }

        $settings->save();

        $events->fire(new CustomerHasUpdatedItsSettings($settings));

        return $settings;
    }
}
