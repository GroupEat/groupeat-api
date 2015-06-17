<?php
namespace Groupeat\Settings\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Migrations\CustomerSettingMigration;
use Groupeat\Support\Entities\Abstracts\Entity;

class CustomerSetting extends Entity
{
    /**
     * @param string   $label
     * @param Customer $customer
     * @param mixed    $value
     */
    public static function setByLabel($label, Customer $customer, $value)
    {
        static::set(Setting::findByLabelOrFail($label), $customer, $value);
    }

    /**
     * @param Setting  $defaultSetting
     * @param Customer $customer
     * @param mixed    $value
     */
    public static function set(Setting $defaultSetting, Customer $customer, $value)
    {
        $value = $defaultSetting->applyCasting($value);

        $existingSetting = static::query()
            ->where('customerId', $customer->id)
            ->where('settingId', $defaultSetting->id)
            ->first();

        if (!is_null($existingSetting)) {
            $existingSetting->setting()->associate($defaultSetting);

            if ($existingSetting->value != $value) {
                $existingSetting->value = $value;
                $existingSetting->save();
            }
        } else {
            $newSetting = new static;
            $newSetting->customer()->associate($customer);
            $newSetting->setting()->associate($defaultSetting);
            $newSetting->value = $value;
            $newSetting->save();
        }
    }

    public function getRules()
    {
        return [
            'customerId' => 'required',
            'settingId' => 'required',
            'value' => 'required',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }

    protected function getRelatedMigration()
    {
        return new CustomerSettingMigration;
    }

    protected function hasCast($key)
    {
        if ($key == 'value') {
            return true;
        } else {
            return parent::hasCast($key);
        }
    }

    protected function getCastType($key)
    {
        if ($key == 'value') {
            return $this->setting->cast;
        } else {
            return parent::getCastType($key);
        }
    }
}
