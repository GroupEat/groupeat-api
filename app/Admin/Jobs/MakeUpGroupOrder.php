<?php
namespace Groupeat\Admin\Jobs;

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
    private $foodRushDurationInMinutes;

    public function __construct(
        Restaurant $restaurant,
        DiscountRate $initialDiscountRate,
        int $foodRushDurationInMinutes
    ) {
        $this->restaurant = $restaurant;
        $this->initialDiscountRate = $initialDiscountRate;
        $this->foodRushDurationInMinutes = $foodRushDurationInMinutes;
    }

    public function handle(Dispatcher $events)
    {
        if ($this->initialDiscountRate->percentage > $this->restaurant->maximumDiscountRate) {
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
        $groupOrder->createdAt = $groupOrder->freshTimestamp();
        $groupOrder->endingAt = $groupOrder->createdAt->copy()->addMinutes($this->foodRushDurationInMinutes);

        $groupOrder->save();

        $events->fire(new GroupOrderHasBeenMadeUp($groupOrder));

        return $groupOrder;
    }
}
