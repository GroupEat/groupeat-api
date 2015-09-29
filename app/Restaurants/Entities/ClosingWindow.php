<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Restaurants\Entities\Abstracts\Window;

class ClosingWindow extends Window
{
    protected $dates = ['start', 'end'];
}
