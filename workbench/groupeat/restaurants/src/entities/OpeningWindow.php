<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Restaurants\Entities\Abstracts\Window;

class OpeningWindow extends Window {

    public function getRules()
    {
        $rules = parent::getRules();

        $rules['dayOfWeek'] = 'required|in:'.implode(',', range(0, 6));

        return $rules;
    }

}
