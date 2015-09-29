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

    protected function getStartAttribute()
    {
        return $this->createCarbonFromTime('start');
    }

    protected function getEndAttribute()
    {
        return $this->createCarbonFromTime('end');
    }

    protected function createCarbonFromTime($attribute)
    {
        return Carbon::createFromFormat('H:i:s', $this->attributes[$attribute]);
    }
}
