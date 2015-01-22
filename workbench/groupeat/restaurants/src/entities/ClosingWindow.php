<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Restaurants\Entities\Abstracts\Window;

class ClosingWindow extends Window {

    public function getRules()
    {
        $rules = parent::getRules();

        $rules['day'] = 'required|date';

        return $rules;
    }

}
