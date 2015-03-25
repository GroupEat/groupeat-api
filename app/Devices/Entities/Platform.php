<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasLabel;

class Platform extends Entity
{
    use HasLabel;

    public $timestamps = false;

    public function getRules()
    {
        return [
            'label' => $this->labelRules,
        ];
    }
}
