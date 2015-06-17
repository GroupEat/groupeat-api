<?php
namespace Groupeat\Settings\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasLabel;

class Setting extends Entity
{
    use HasLabel;

    public function getRules()
    {
        return [
            'cast' => 'required',
            'label' => $this->labelRules,
            'default' => 'required',
        ];
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function applyCasting($value)
    {
        return $this->castAttribute('default', $value);
    }

    protected function hasCast($key)
    {
        if ($key == 'default') {
            return true;
        } else {
            return parent::hasCast($key);
        }
    }

    protected function getCastType($key)
    {
        if ($key == 'default') {
            return $this->attributes['cast'];
        } else {
            return parent::getCastType($key);
        }
    }
}
