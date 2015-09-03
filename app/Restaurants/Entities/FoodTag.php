<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasLabel;

class FoodTag extends Entity
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
