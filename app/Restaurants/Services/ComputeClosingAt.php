<?php
namespace Groupeat\Restaurants\Services;

use Carbon\Carbon;
use Groupeat\Restaurants\Entities\ClosingWindow;
use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Entities\Restaurant;
use League\Period\Period;

class ComputeClosingAt
{
    public function call(Restaurant $restaurant): Carbon
    {
        $now = Carbon::now();
        $openings = collect($restaurant->openingWindows);
        $closings = collect($restaurant->closingWindows);
        $currentOpeningWindow = $openings->first(function ($key, OpeningWindow $opening) use ($now) {
            return $now->dayOfWeek == $opening->dayOfWeek &&
                $now->copy()->setTimeFromTimeString($opening->rawStart) <= $now &&
                $now < $now->copy()->setTimeFromTimeString($opening->rawEnd);
        });

        if (!$currentOpeningWindow) {
            return $now;
        }

        $currentOpeningPeriod = new Period(
            $now->copy()->setTimeFromTimeString($currentOpeningWindow->rawStart),
            $now->copy()->setTimeFromTimeString($currentOpeningWindow->rawEnd)
        );

        if ($currentOpeningWindow->rawEnd == '23:59:59') {
            $nextDay = $now->copy()->addDay();
            $contiguousNextDayOpeningWindow = $openings->first(function ($key, OpeningWindow $opening) use ($nextDay) {
                return $nextDay->dayOfWeek == $opening->dayOfWeek && $opening->rawStart == '00:00:00';
            });

            if ($contiguousNextDayOpeningWindow) {
                $currentOpeningPeriod = $currentOpeningPeriod->merge(new Period(
                    $nextDay->copy()->setTimeFromTimeString($contiguousNextDayOpeningWindow->rawStart),
                    $nextDay->copy()->setTimeFromTimeString($contiguousNextDayOpeningWindow->rawEnd)
                ));
            }
        }

        $interruptingClosingWindow = $closings->first(function ($key, ClosingWindow $closing) use ($currentOpeningPeriod) {
            $closingPeriod = new Period($closing->start, $closing->end);

            return $closingPeriod->overlaps($currentOpeningPeriod);
        });

        if (!$interruptingClosingWindow) {
            return Carbon::instance($currentOpeningPeriod->getEndDate());
        }

        return $interruptingClosingWindow->start;
    }
}
