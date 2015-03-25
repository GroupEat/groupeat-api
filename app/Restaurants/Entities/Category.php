<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasLabel;

class Category extends Entity
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
