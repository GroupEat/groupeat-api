<?php namespace Groupeat\Restaurants;

use Groupeat\Auth\Auth;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Orders\Events\GroupOrderHasEnded;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Handlers\Events\SendGroupOrderHasEndedMail;
use Groupeat\Restaurants\Handlers\Events\SendOrderHasBeenPlacedMail;
use Groupeat\Restaurants\Values\ConfirmationTokenDurationInMinutes;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Restaurants\Values\MinimumOpeningDurationInMinutes;
use Groupeat\Support\Providers\Abstracts\WorkbenchPackageProvider;

class PackageProvider extends WorkbenchPackageProvider
{
    protected function registerPackage()
    {
        $this->bindValueFromConfig(
            MaximumDeliveryDistanceInKms::class,
            'restaurants.around_distance_in_kilometers'
        );

        $this->bindValueFromConfig(
            MinimumOpeningDurationInMinutes::class,
            'restaurants.opening_duration_in_minutes'
        );

        $this->bindValueFromConfig(
            ConfirmationTokenDurationInMinutes::class,
            2 * $this->app['config']->get('orders.maximum_preparation_time_in_minutes')
        );
    }

    protected function bootPackage()
    {
        $this->app[Auth::class]->addUserType(new Restaurant);

        $this->listen(GroupOrderHasBeenCreated::class, SendOrderHasBeenPlacedMail::class, 'created');
        $this->listen(GroupOrderHasBeenJoined::class, SendOrderHasBeenPlacedMail::class, 'joined');
        $this->listen(GroupOrderHasEnded::class, SendGroupOrderHasEndedMail::class);
    }
}
