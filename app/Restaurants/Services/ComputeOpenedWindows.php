<?php
namespace Groupeat\Restaurants\Services;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\Restaurant;
use League\Period\Period;
use Groupeat\Restaurants\Entities\ClosingWindow;

class ComputeOpenedWindows
{
    public function call(Restaurant $restaurant, Carbon $start, Carbon $end)
    {
        $openings = collect($restaurant->openingWindows);
        $closings = collect($restaurant->closingWindows);
        $now = Carbon::now();

        // Adding a closing for today until now to prevent getting opened windows in the past
        $beforeNowClosing = new ClosingWindow;
        $beforeNowClosing->start = $now->copy()->startOfDay()->toDateTimeString();
        $beforeNowClosing->end = $now->toDateTimeString();
        $closings->push($beforeNowClosing);

        $openedPeriods = [];
        foreach ($this->getOpeningPeriods($openings, $start, $end) as $openingPeriod) {
            $openedPeriods = array_merge($openedPeriods, $this->getOpenedPeriods($openingPeriod, $closings));
        }
        return $openedPeriods;
    }

    public function getOpeningPeriods($openings, Carbon $start, Carbon $end)
    {
        $openingPeriods = [];
        $currentDay = $start->copy();
        while ($currentDay->lte($end)) {
            foreach ($openings as $opening) {
                if ($opening->dayOfWeek == $currentDay->dayOfWeek) {
                    $openingPeriodStart = $currentDay->copy()->setTime($opening->start->hour, $opening->start->minute, $opening->start->second);
                    $openingPeriodEnd = $currentDay->copy()->setTime($opening->end->hour, $opening->end->minute, $opening->end->second);
                    array_push($openingPeriods, new Period($openingPeriodStart, $openingPeriodEnd));
                }
            }
            $currentDay = $currentDay->addDay();
        }

        return $openingPeriods;
    }

    public function getOpenedPeriods(Period $openingPeriod, $closings)
    {
        $openedPeriods = [$openingPeriod];
        foreach ($closings as $closing) {
            $openedPeriodsCopy = $openedPeriods;
            $openedPeriods = [];
            foreach ($openedPeriodsCopy as $openedPeriod) {
                $closingPeriod = new Period($closing->start, $closing->end);
                // Keeping the whole $openedPeriod if it does not overlap with the closing period
                // Otherwise fetches the non-overlapping sections
                if ($openedPeriod->overlaps($closingPeriod)) {
                    foreach ($openedPeriod->diff($closingPeriod) as $diff) {
                        if ($diff->overlaps($openedPeriod)) {
                            array_push($openedPeriods, $diff->intersect($openedPeriod));
                        }
                    }
                } else {
                    array_push($openedPeriods, $openedPeriod);
                }
            }
        }

        return $openedPeriods;
    }
}
