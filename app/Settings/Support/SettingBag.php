<?php
namespace Groupeat\Settings\Support;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Entities\CustomerSetting;
use Groupeat\Settings\Entities\Setting;
use Groupeat\Support\Exceptions\Exception;
use JsonSerializable;

class SettingBag implements JsonSerializable
{
    /**
     * @var array
     */
    private $values = [];

    public function __construct(Customer $customer)
    {
        $customerSettings = CustomerSetting::query()
            ->where('customerId', $customer->id)
            ->with('setting')
            ->get()
            ->each(function (CustomerSetting $customerSetting) {
                $this->values[$customerSetting->setting->label] = $customerSetting->value;
            });
    }

    /**
     * @param string $label
     *
     * @return bool
     */
    public function has($label)
    {
        return isset($this->values[$label]);
    }

    /**
     * @param string $label
     *
     * @return mixed
     */
    public function get($label)
    {
        if (!$this->has($label)) {
            $this->throwUnexisting($label);
        }

        return $this->values[$label];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->all();
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->all());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    private function throwUnexisting($label)
    {
        throw new Exception(
            'unexistingSettingLabel',
            "The label $label does not correspond to any setting."
        );
    }
}
