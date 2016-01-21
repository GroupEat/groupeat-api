<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenConfirmed;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Events\Dispatcher;

class ConfirmGroupOrder extends Job
{
    private $groupOrder;
    private $preparedAt;

    public function __construct(GroupOrder $groupOrder, Carbon $preparedAt)
    {
        $this->groupOrder = $groupOrder;
        $this->preparedAt = $preparedAt;
    }

    public function handle(Dispatcher $events, MaximumPreparationTimeInMinutes $maximumPreparationTimeInMinutes)
    {
        $this->assertNotAlreadyConfirmed();
        $this->guardAgainstInvalidPreparationTime($maximumPreparationTimeInMinutes->value());

        $this->groupOrder->confirm($this->preparedAt);

        $events->fire(new GroupOrderHasBeenConfirmed($this->groupOrder));
    }

    private function assertNotAlreadyConfirmed()
    {
        if (!empty($this->groupOrder->confirmedAt)) {
            throw new UnprocessableEntity(
                'alreadyConfirmed',
                "The {$this->groupOrder->toShortString()} has already been confirmed at {$this->groupOrder->confirmedAt}."
            );
        }
    }

    private function guardAgainstInvalidPreparationTime(int $maximumPreparationTimeInMinutes)
    {
        $closedAt = $this->groupOrder->closedAt;

        if ($this->preparedAt < $closedAt) {
            throw new UnprocessableEntity(
                'cannotBePreparedBeforeBeingClosed',
                "The {$this->groupOrder->toShortString()} cannot be completely prepared before being closed."
            );
        }

        $preparationTimeInMinutes = $closedAt->diffInMinutes($this->preparedAt, false);

        if ($preparationTimeInMinutes > $maximumPreparationTimeInMinutes) {
            throw new UnprocessableEntity(
                'preparationTimeTooLong',
                "The preration time of the {$this->groupOrder->toShortString()} "
                . "should not exceed {$maximumPreparationTimeInMinutes} "
                . "minutes, $preparationTimeInMinutes given."
            );
        }
    }
}
