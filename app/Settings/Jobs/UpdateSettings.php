<?php
namespace Groupeat\Settings\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Jobs\Abstracts\Command;

class UpdateSettings extends Command
{
    private $customer;
    private $values;

    public function __construct(Customer $customer, array $values)
    {
        $this->customer = $customer;
        $this->values = $values;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getValues()
    {
        return $this->values;
    }
}
