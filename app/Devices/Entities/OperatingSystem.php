<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;

class OperatingSystem extends Entity
{
    public $timestamps = false;

    public function getRules()
    {
        return [
            'label' => 'required',
        ];
    }
}
