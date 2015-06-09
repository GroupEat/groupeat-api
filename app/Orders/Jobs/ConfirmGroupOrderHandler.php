<?php
namespace Groupeat\Orders\Jobs;

use Carbon\Carbon;
use Groupeat\Orders\Jobs\ConfirmGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Events\GroupOrderHasBeenConfirmed;
use Groupeat\Orders\Values\MaximumPreparationTimeInMinutes;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;

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
        $closedAt = $groupOrder->closedAt;

        if ($preparedAt < $closedAt) {
            throw new UnprocessableEntity(
                'cannotBePreparedBeforeBeingClosed',
                "The {$groupOrder->toShortString()} cannot be completely prepared before being closed."
            );
        }

        $preparationTimeInMinutes = $closedAt->diffInMinutes($preparedAt, false);

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
        if (!empty($groupOrder->confirmedAt)) {
            throw new UnprocessableEntity(
                'alreadyConfirmed',
                "The {$groupOrder->toShortString()} has already been confirmed at {$groupOrder->confirmedAt}."
            );
        }
    }
}
