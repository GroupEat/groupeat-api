<?php
namespace Groupeat\Orders\Handlers\Commands;

use Carbon\Carbon;
use Groupeat\Orders\Commands\ConfirmGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenConfirmed;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Events\Dispatcher;

class ConfirmGroupOrderHandler
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

    public function handle(ConfirmGroupOrder $command)
    {
        $groupOrder = $command->getGroupOrder();
        $preparedAt = $command->getPreparedAt();

        $this->assertNotAlreadyConfirmed($groupOrder);
        $this->guardAgainstInvalidPreparationTime($groupOrder, $preparedAt);

        $groupOrder->confirm($preparedAt);

        $this->events->fire(new GroupOrderHasBeenConfirmed($groupOrder));
    }

    public function getMaximumPreparationTimeInMinutes()
    {
        return $this->maximumPreparationTimeInMinutes;
    }

    private function guardAgainstInvalidPreparationTime(GroupOrder $groupOrder, Carbon $preparedAt)
    {
        $completedAt = $groupOrder->completed_at;

        if ($preparedAt < $completedAt) {
            throw new UnprocessableEntity(
                'cannotBePreparedBeforeBeingCompleted',
                "The {$groupOrder->toShortString()} cannot be completely prepared before being completed."
            );
        }

        $preparationTimeInMinutes = $completedAt->diffInMinutes($preparedAt, false);

        if ($preparationTimeInMinutes > $this->maximumPreparationTimeInMinutes) {
            throw new UnprocessableEntity(
                'preparationTimeTooLong',
                "The preration time of the {$groupOrder->toShortString()} "
                . "should not exceed {$this->maximumPreparationTimeInMinutes} "
                . "minutes, $preparationTimeInMinutes given."
            );
        }
    }

    private function assertNotAlreadyConfirmed(GroupOrder $groupOrder)
    {
        if (!empty($groupOrder->confirmed_at)) {
            throw new UnprocessableEntity(
                'alreadyConfirmed',
                "The {$groupOrder->toShortString()} has already been confirmed at {$groupOrder->confirmed_at}."
            );
        }
    }
}
