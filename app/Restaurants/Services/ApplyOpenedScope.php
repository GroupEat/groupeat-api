<?php
namespace Groupeat\Restaurants\Services;

use Carbon\Carbon;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Orders\Values\MaximumFoodrushInMinutes;
use Groupeat\Orders\Values\MinimumFoodrushInMinutes;
use Groupeat\Restaurants\Entities\OpeningWindow;
use Illuminate\Database\Eloquent\Builder;
use League\Period\Period;

class ApplyOpenedScope
{
    private $minimumFoodrushInMinutes;
    private $maximumFoodrushInMinutes;
    private $maximumPreparationTimeInMinutes;

    public function __construct(
        MinimumFoodrushInMinutes $minimumFoodrushInMinutes,
        MaximumFoodrushInMinutes $maximumFoodrushInMinutes,
        MaximumPreparationTimeInMinutes $maximumPreparationTimeInMinutes
    ) {
        $this->minimumFoodrushInMinutes = $minimumFoodrushInMinutes->value();
        $this->maximumFoodrushInMinutes = $maximumFoodrushInMinutes->value();
        $this->maximumPreparationTimeInMinutes = $maximumPreparationTimeInMinutes->value();
    }

    public function call(Builder $query, Period $period = null)
    {
        if (is_null($period)) {
            $start = Carbon::now()->addMinutes($this->minimumFoodrushInMinutes);
            $end = Carbon::now()
                ->addMinutes($this->maximumFoodrushInMinutes)
                ->addMinutes($this->maximumPreparationTimeInMinutes);

            $period = new Period($start, $end);
        } else {
            $start = Carbon::instance($period->getStartDate());
            $end = Carbon::instance($period->getEndDate());
        }

        $this->assertSameOrPreviousDay($start, $end);

        if ($this->isSpanningOnTwoDays($start, $end)) {
            $this->applySpanningOnTwoDaysScope($query, $start, $end);
        } else {
            $this->applySameDayScope($query, $start, $end);
        }
    }

    private function applySameDayScope(Builder $query, Carbon $start, Carbon $end)
    {
        $query
            ->whereHas(
                'openingWindows',
                function (Builder $subQuery) use ($start, $end) {
                    $openingWindow = $subQuery->getModel();

                    $subQuery
                        ->where($openingWindow->getTableField('dayOfWeek'), $start->dayOfWeek)
                        ->where($openingWindow->getTableField('start'), '<=', $start->toTimeString())
                        ->where($openingWindow->getTableField('end'), '>=', $end->toTimeString());
                }
            )
            ->whereDoesntHave(
                'closingWindows',
                function (Builder $subQuery) use ($start, $end) {
                    $closingWindow = $subQuery->getModel();

                    $subQuery
                        ->where($closingWindow->getTableField('start'), '<=', $end)
                        ->where($closingWindow->getTableField('end'), '>=', $start);
                }
            );
    }

    private function applySpanningOnTwoDaysScope(Builder $query, Carbon $start, Carbon $end)
    {
        $query
            ->whereHas(
                'openingWindows',
                function (Builder $subQuery) use ($start, $end) {
                    $openingWindow = $subQuery->getModel();

                    $subQuery->where(function (Builder $miniQuery) use ($start, $end, $openingWindow) {
                        $miniQuery
                            ->orWhere(function (Builder $tinyQuery) use ($start, $openingWindow) {
                                $midnight = $start->copy()->hour(23)->minute(59)->second(59);

                                $tinyQuery->where($openingWindow->getTableField('dayOfWeek'), $start->dayOfWeek)
                                    ->where($openingWindow->getTableField('start'), '<=', $start->toTimeString())
                                    ->where($openingWindow->getTableField('end'), '>=', $midnight);
                            })
                            ->orWhere(function (Builder $tinyQuery) use ($end, $openingWindow) {
                                $midnight = $end->copy()->hour(00)->minute(00)->second(00);

                                $tinyQuery->where($openingWindow->getTableField('dayOfWeek'), $end->dayOfWeek)
                                    ->where($openingWindow->getTableField('start'), '<=', $midnight)
                                    ->where($openingWindow->getTableField('end'), '>=', $end->toTimeString());
                            });
                    });
                }
            )
            ->whereDoesntHave(
                'closingWindows',
                function (Builder $subQuery) use ($start, $end) {
                    $closingWindow = $subQuery->getModel();

                    $subQuery->where(function (Builder $miniQuery) use ($start, $end, $closingWindow) {
                        $miniQuery
                            ->orWhere(function (Builder $tinyQuery) use ($start, $closingWindow) {
                                $midnight = $start->copy()->hour(23)->minute(59)->second(59);

                                $tinyQuery
                                    ->where($closingWindow->getTableField('start'), '<', $midnight)
                                    ->where($closingWindow->getTableField('end'), '>=', $start);
                            })
                            ->orWhere(function (Builder $tinyQuery) use ($end, $closingWindow) {
                                $midnight = $end->copy()->hour(00)->minute(00)->second(00);

                                $tinyQuery
                                    ->where($closingWindow->getTableField('start'), '<=', $end)
                                    ->where($closingWindow->getTableField('end'), '>=', $midnight);
                            });
                    });
                }
            );
    }

    private function assertSameOrPreviousDay(Carbon $start, Carbon $end)
    {
        if (!$start->isSameDay($end) && !$this->isSpanningOnTwoDays($start, $end)) {
            throw new BadRequest(
                'dateTimesMustBeFromSameDay',
                "The DateTime $start must be from the same or previous day than $end."
            );
        }
    }

    private function isSpanningOnTwoDays(Carbon $start, Carbon $end)
    {
        if ($start->gt($end)) {
            throw new BadRequest(
                'dateTimesMustBeInAscendingOrder',
                "The DateTime $start must be less than $end."
            );
        }

        return $start->copy()->addDay()->isSameDay($end);
    }
}
