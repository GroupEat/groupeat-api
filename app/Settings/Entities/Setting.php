<?php
namespace Groupeat\Settings\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasLabel;

class Setting extends Entity
{
    use HasLabel;

    public $timestamps = false;

    public function getRules()
    {
        return [
            'label' => $this->labelRules,
            'default' => 'required',
        ];
    }
}
