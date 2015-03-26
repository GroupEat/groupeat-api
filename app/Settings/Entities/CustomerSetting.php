<?php
namespace Groupeat\Settings\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;

class CustomerSetting extends Entity
{
    public $timestamps = false;

    public function getRules()
    {
        return [
            'customer_id' => 'required',
            'setting_id' => 'required',
            'value' => 'required',
        ];
    }
}
