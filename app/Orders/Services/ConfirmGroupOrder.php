<?php
namespace Groupeat\Orders\Services;

use Carbon\Carbon;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenConfirmed;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Events\Dispatcher;

class ConfirmGroupOrder
{
    private $events;
    private $maximumPreparationTimeInMinutes;

    public function __construct(
        Dispatcher $events,
        MaximumPreparationTimeInMinutes $maximumPreparationTimeInMinutes
    ) {
        $this->events = $events;
        $this->maximumPreparationTimeInMinutes = $maximumPreparationTimeInMinutes->value();
    }

    /**
     * @param GroupOrder $groupOrder
     * @param Carbon     $preparedAt
     *
     * @return Carbon
     */
    public function call(GroupOrder $groupOrder, Carbon $preparedAt)
    {
        // TODO: check that the groupOrder has not already been confirmed
        $this->guardAgainstInvalidPreparationTime($groupOrder->completed_at, $preparedAt);

        $groupOrder->confirm($preparedAt);

        $this->events->fire(new GroupOrderHasBeenConfirmed($groupOrder));

        return $preparedAt;
    }

    public function getMaximumPreparationTimeInMinutes()
    {
        return $this->maximumPreparationTimeInMinutes;
    }

    private function guardAgainstInvalidPreparationTime(Carbon $completedAt, Carbon $preparedAt)
    {
        if ($preparedAt < $completedAt) {
            throw new UnprocessableEntity(
                'cannotBePreparedBeforeBeingCompleted',
                "A group order cannot be completely prepared before being completed."
            );
        }

        $preparationTimeInMinutes = $completedAt->diffInMinutes($preparedAt, false);

        if ($preparationTimeInMinutes > $this->maximumPreparationTimeInMinutes) {
            throw new UnprocessableEntity(
                'preparationTimeTooLong',
                "The preration time should not exceed {$this->maximumPreparationTimeInMinutes} "
                . "minutes, $preparationTimeInMinutes given."
            );
        }
    }
}
