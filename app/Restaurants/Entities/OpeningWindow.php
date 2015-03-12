<?php
namespace Groupeat\Restaurants\Entities;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\Abstracts\Window;

class OpeningWindow extends Window
{
    public function getRules()
    {
        $rules = parent::getRules();

        $rules['dayOfWeek'] = 'required|in:'.implode(',', range(0, 6));

        return $rules;
    }

    protected function getFromAttribute()
    {
        return $this->createCarbonFromTime('from');
    }

    protected function getToAttribute()
    {
        return $this->createCarbonFromTime('to');
    }

    protected function createCarbonFromTime($attribute)
    {
        return Carbon::createFromFormat('H:i:s', $this->attributes[$attribute]);
    }
}
