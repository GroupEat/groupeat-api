<?php
namespace Groupeat\Restaurants\Http\V1;

use League\Fractal\TransformerAbstract;
use League\Period\Period;

class PeriodTransformer extends TransformerAbstract
{
    public function transform(Period $period)
    {
        return [
            'start' => $period->getStartDate(),
            'end' => $period->getEndDate(),
        ];
    }
}
