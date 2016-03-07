<?php
namespace Groupeat\Restaurants\Services;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\Restaurant;
use League\Period\Period;
use Groupeat\Restaurants\Entities\ClosingWindow;
use Illuminate\Database\Eloquent\Collection;

class ComputeOpenedWindows
{
    public function call(Restaurant $restaurant, Period $period): Collection
    {
        // Adding a closing for today until now to prevent getting opened windows in the past
        $beforeNowClosing = new ClosingWindow;
        $beforeNowClosing->start = Carbon::instance($period->getStartDate())->startOfDay();
        $beforeNowClosing->end = $period->getStartDate();
        $restaurant->closingWindows[] = $beforeNowClosing;

        $openedPeriods = new Collection;
        foreach ($this->makeOpeningPeriodsFromOpeningWindows($restaurant->openingWindows, $period) as $openingPeriod) {
            $newOpenedPeriods = $this->applyClosingWindowsOnOpeningPeriods($openingPeriod, $restaurant->closingWindows);
            foreach ($newOpenedPeriods as $newOpenedPeriod) {
                $openedPeriods[] = $newOpenedPeriod;
            }
        }

        return $openedPeriods;
    }

    private function makeOpeningPeriodsFromOpeningWindows(Collection $openings, Period $period): Collection
    {
        $openingPeriods = new Collection;
        $currentDay = Carbon::instance($period->getStartDate());

        while ($currentDay <= Carbon::instance($period->getEndDate())) {
            foreach ($openings as $opening) {
                if ($opening->dayOfWeek == $currentDay->dayOfWeek) {
                    $openingPeriodStart = $this->makeCarbonWithDateAndTime($currentDay, $opening->start);
                    $openingPeriodEnd = $this->makeCarbonWithDateAndTime($currentDay, $opening->end);
                    $openingPeriods[] = new Period($openingPeriodStart, $openingPeriodEnd);
                }
            }
            $currentDay = $currentDay->addDay();
        }

        return $this->mergeOpeningWindowsAroundMidnight($openingPeriods);
    }

    private function mergeOpeningWindowsAroundMidnight(Collection $openingPeriods): Collection
    {
        $mergedPeriods = new Collection;

        for ($i = 0; $i < $openingPeriods->count(); $i++) {
            if (Carbon::instance($openingPeriods[$i]->getEndDate())->toTimeString() == '23:59:59'
                && $i !== $openingPeriods->count() - 1
                && Carbon::instance($openingPeriods[$i + 1]->getStartDate())->toTimeString() == '00:00:00'
            ) {
                $mergedPeriods[] = new Period(
                    $openingPeriods[$i]->getStartDate(),
                    $openingPeriods[$i + 1]->getEndDate()
                );
                $i++;
            } else {
                $mergedPeriods[] = $openingPeriods[$i];
            }
        }

        return $mergedPeriods;
    }

    private function applyClosingWindowsOnOpeningPeriods(Period $openingPeriod, Collection $closings): Collection
    {
        $openedPeriods = collect([$openingPeriod]);

        foreach ($closings as $closing) {
            $openedPeriodsCopy = $openedPeriods;
            $openedPeriods = new Collection;
            foreach ($openedPeriodsCopy as $openedPeriod) {
                $closingPeriod = new Period($closing->start, $closing->end);
                // Keeping the whole $openedPeriod if it does not overlap with the closing period
                // Otherwise fetches the non-overlapping sections
                if ($openedPeriod->overlaps($closingPeriod)) {
                    foreach ($openedPeriod->diff($closingPeriod) as $diff) {
                        if ($diff->overlaps($openedPeriod)) {
                            $openedPeriods[] = $diff->intersect($openedPeriod);
                        }
                    }
                } else {
                    $openedPeriods[] = $openedPeriod;
                }
            }
        }

        return $openedPeriods;
    }

    private function makeCarbonWithDateAndTime(Carbon $date, Carbon $time): Carbon
    {
        return $date->copy()->setTime($time->hour, $time->minute, $time->second);
    }
}
