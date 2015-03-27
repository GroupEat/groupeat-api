<?php
namespace Groupeat\Settings\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Settings\Migrations\CustomerSettingMigration;
use Groupeat\Support\Entities\Abstracts\Entity;

class CustomerSetting extends Entity
{
    public $timestamps = false;

    /**
     * @param Customer $customer
     * @param string   $label
     * @param mixed    $value
     */
    public static function set(Customer $customer, $label, $value)
    {
        $model = new static;
        $defaultSetting = Setting::findByLabelOrFail($label);
        $value = $defaultSetting->applyCasting($value);
        $existingSetting = $model
            ->where('customer_id', $customer->id)
            ->where('setting_id', $defaultSetting->id)
            ->first();

        if (!is_null($existingSetting)) {
            if ($existingSetting->value != $value) {
                if ($defaultSetting->default == $value) {
                    $existingSetting->delete(); // The default value will be used
                } else {
                    $existingSetting->value = $value;
                    $existingSetting->save();
                }
            }
        } elseif ($defaultSetting->default != $value) {
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
            'customer_id' => 'required',
            'setting_id' => 'required',
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
