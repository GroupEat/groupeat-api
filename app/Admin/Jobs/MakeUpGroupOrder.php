<?php
namespace Groupeat\Admin\Jobs;

use Carbon\Carbon;
use Groupeat\Admin\Events\GroupOrderHasBeenMadeUp;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Support\DiscountRate;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Events\Dispatcher;

class MakeUpGroupOrder extends Job
{
    private $restaurant;
    private $initialDiscountRate;
    private $endingAt;

    public function __construct(
        Restaurant $restaurant,
        DiscountRate $initialDiscountRate,
        Carbon $endingAt
    ) {
        $this->restaurant = $restaurant;
        $this->initialDiscountRate = $initialDiscountRate;
        $this->endingAt = $endingAt;
    }

    public function handle(Dispatcher $events)
    {
        if ($this->initialDiscountRate->toPercentage() > $this->restaurant->maximumDiscountRate->toPercentage()) {
            throw new BadRequest(
                'initialDiscountRateTooBig',
                "The initial discount rate cannot exceed the maximum discount rate of the restaurant"
            );
        }

        GroupOrder::assertNotExistingFor($this->restaurant);
        $groupOrder = new GroupOrder;
        $groupOrder->isMadeUp = true;
        $groupOrder->restaurant()->associate($this->restaurant);
        $groupOrder->discountRate = $this->initialDiscountRate;
        // No validation step is run here because creating an made-up group order is an admin decision.
        // Thus, the input can be trusted to be correct.
        $groupOrder->endingAt = $this->endingAt;

        $groupOrder->save();

        $events->fire(new GroupOrderHasBeenMadeUp($groupOrder));

        return $groupOrder;
    }
}
